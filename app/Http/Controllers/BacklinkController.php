<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Backlinks, Website, User, Keyword};

class BacklinkController extends Controller
{
    public function index(Request $request)
    {
        $query = Backlinks::query()->with('user')->where('website_id', auth()->user()->website_id);
        // if (auth()->user()->hasRole(['Admin', 'Super Admin'])) {
        //     $query->where('aproval_status', 'Approved');
        // }
        if (!auth()->user()->hasRole(['Admin', 'Super Admin'])) {
            $query->where('user_id', auth()->user()->id)
                ->where('website_id', auth()->user()->website_id);
        }

        $filterQuery = $query->clone();
        if ($request->filled('link_type')) {
            $query->where('link_type', $request->input('link_type'));
        }

        if ($request->filled('status') && in_array($request->input('status'), ['Active', 'Declined'])) {
            $query->where('status', $request->input('status'));
        }
        if ($request->filled('status') && $request->input('status') == 'Pending' && auth()->user()->hasRole('Admin')) {
            $query->where('status', 'Pending');
            $query->whereHas('user', function ($query) {
                $query->where('parent_id', auth()->user()->id);
            })
            ->orWhere('user_id', auth()->user()->id);
        }
        if ($request->filled('status') && $request->input('status') == 'Pending' && auth()->user()->hasRole('User')) {
            $query->where('status', 'Pending')->where('user_id', auth()->user()->id);
        }
        if ($request->filled('status') && $request->input('status') == 'Pending' && auth()->user()->hasRole('Super Admin')) {
            $query->where('status', 'Pending');
        }
        // if request has no status or null get only approved backlinks
        if (!$request->filled('status') && !auth()->user()->hasRole(['Admin', 'Super Admin'])) {
            $query->where('status', 'Active');
        }
        if ($request->filled('daterange')) {
            $dates = explode(' - ', $request->input('daterange'));
            if (count($dates) == 2) {
                $startDate = date('Y-m-d 00:00:00', strtotime($dates[0]));
                $endDate = date('Y-m-d 23:59:59', strtotime($dates[1]));
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }
        }

        if ($request->filled('user')) {
            $query->where('user_id', $request->input('user'));
        }

        $backlinks = $query->get();
        $filterBacklinks = $filterQuery->get();
        $totallinks = $filterBacklinks->count();
        $activelinks = $filterBacklinks->where("status", "Active")->count();
        $inactivelinks = $filterBacklinks->where("status", "Inactive")->count();
        $pendinglinks = $filterBacklinks->where("status", "Pending")->count();
        $declinedlinks = $filterBacklinks->where("status", "Declined")->count();

        $data_name = ['Active', 'Inactive', 'Pending', 'Declined'];
        $pie_data = [
            ["name" => "Active", "value" => $activelinks],
            ["name" => "Inactive", "value" => $inactivelinks],
            ["name" => "Pending", "value" => $pendinglinks],
            ["name" => "Declined", "value" => $declinedlinks],
        ];

        $values = [];
        $uniqueDomains = [];
        foreach ($backlinks as $data) {
            $values[] = [
                "name" => $data->website,
                "data" => [
                    "url" => $data->website,
                    "da" => $data->domain_authority,
                    "pa" => $data->page_authority
                ]
            ];

            $domain = parse_url($data->url, PHP_URL_HOST);
            if ($domain) {
                $domain = preg_replace('/^www\./', '', $domain);
                $uniqueDomains[$domain] = true;
            }
        }

        $uniqueDomainCount = count($uniqueDomains);

        $domain_authority = $backlinks->sum("domain_authority");
        $page_authority = $backlinks->sum("page_authority");
        $backlink_users = Backlinks::get()->pluck('user_id')->unique();
        $users = User::whereIn('id', $backlink_users)->get();

        return view('backlinks.list', compact([
            'backlinks',
            'pie_data',
            'data_name',
            'values',
            'domain_authority',
            'page_authority',
            'users',
            'request',
            'activelinks',
            'inactivelinks',
            'pendinglinks',
            'declinedlinks',
            'totallinks',
            'uniqueDomainCount'
        ]));
    }

    public function storeOrUpdate(Request $request, $id = null)
    {
        if ($request->isMethod('GET')) {
            try {
                $websites = Website::all();
                $keywords = Keyword::where('website_id', auth()->user()->website_id)->get();
                $backlink = $id ? Backlinks::findOrFail($id) : null;
                session(['url.intended' => url()->previous()]);
                return view('backlinks.create-update', compact('websites', 'backlink', 'keywords'));
            } catch (\Exception $e) {
                return redirect()->route('backlinks.index')->with([
                    'status' => 'danger',
                    'message' => 'An error occurred while fetching the data!'
                ]);
            }
        } elseif ($request->isMethod('POST') || $request->isMethod('PUT')) {
            $rules = [
                'keyword_id' => 'required|exists:keywords,id',
                'website' => 'required|string',
                'url' => 'required|string',
                'backlink_source' => 'required|string',
                'link_type' => 'required|string',
                'spam_score' => 'required|integer',
                'anchor_text' => 'required|string',
                'domain_authority' => 'required|integer',
                'page_authority' => 'required|integer',
                'contact_person' => 'required|string',
                'status' => '',
                'email' => '',
                'password' => '',
                'login_url' => '',
                'company_name' => '',
                'notes_comments' => '',
            ];
            $validatedData = $request->validate($rules);
            // dd($validatedData);
            try {
                if ($id) {
                    $backlink = Backlinks::findOrFail($id);
                    $backlink->update($validatedData);
                    $message = 'Backlink updated successfully!';
                } else {
                    $validatedData['website_id'] = auth()->user()->website_id;
                    $validatedData['user_id'] = auth()->user()->id;
                    $validatedData['status'] = 'Pending';
                    $backlink = Backlinks::create($validatedData);
                    $message = 'Backlink created successfully!';
                }
                if (session()->has('url.intended')) {
                    $route = session('url.intended');
                    // destroy the session after use
                    session()->forget('url.intended');
                    return redirect($route)->with([
                        'status' => 'success',
                        'message' => $message
                    ]);
                }

                return redirect()->route('backlinks.index')->with([
                    'status' => 'success',
                    'message' => $message
                ]);
            } catch (\Exception $e) {
                return redirect()->route('backlinks.index')->with([
                    'status' => 'danger',
                    'message' => 'An error occurred while processing the data!'
                ]);
            }
        }
    }

    public function destroy(Request $request,  $id = null)
    {
        if ($id) {
            $backlink = Backlinks::find($id);
            if ($backlink) {
                $backlink->delete();
                return redirect()->route('backlinks.index')->with(['status' => 'success', 'message' => 'Backlink has been deleted!']);
            } else {
                return redirect()->route('backlinks.index')->with(['status' => 'danger', 'message' => 'Backlink not found!']);
            }
        }
    }

    public function statusList($approve_status)
    {
        $approvalStatus = $approve_status ?? 'Pending';
        $user_id = auth()->user()->id;

        $backlinks = Backlinks::query()
            ->with('user')
            ->where('website_id', auth()->user()->website_id)
            ->where('aproval_status', $approvalStatus);

        if (auth()->user()->hasRole('Admin')) {
            $backlinks->whereHas('user', function ($query) use ($user_id) {
                $query->where('parent_id', $user_id);
            });
        } elseif (auth()->user()->hasRole('Super Admin')) {
        }else {
            $backlinks->where('user_id', $user_id);
        }

        $backlinks = $backlinks->get();

        return view('backlinks.status-view', compact('backlinks', 'approvalStatus'));
    }

    public function approve(Request $request, $id = null)
    {
        if ($id == null) {
            return response()->json(['status' => 'not found', 'message' => 'Backlink not found!']);
        }

        $backlink = Backlinks::find($id);
        if (!$backlink) {
            return response()->json(['status' => 'danger', 'message' => 'Backlink not found!']);
        }

        if ($request->isMethod('get')) {
            return response()->json(['status' => 'success', 'backlink' => $backlink]);
        }

        if ($request->isMethod('post')) {
            $backlink->aproval_status = $request->input('status');
            $backlink->reason = $request->input('reason') ?? null;
            $backlink->approved_by = auth()->user()->id;
            $backlink->update();

            return response()->json(['status' => 'success', 'message' => 'Backlink status updated!']);
        }

        return response()->json(['status' => 'error', 'message' => 'Invalid request method!']);
    }
}
