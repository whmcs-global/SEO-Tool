<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Backlinks,Website, User};
class BacklinkController extends Controller
{   
    public function index(Request $request)
    {
        $query = Backlinks::query()->with('user');
    
        if (!auth()->user()->hasRole(['Admin', 'Super Admin'])) {
            $query->where('user_id', auth()->user()->id)
                  ->where('website_id', auth()->user()->website_id);
        }
    
        if ($request->filled('link_type')) {
            $query->where('link_type', $request->input('link_type'));
        }
    
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
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
        $totallinks = $backlinks->count();
        $activelinks = $backlinks->where("status", "Active")->count();
        $inactivelinks = $backlinks->where("status", "Inactive")->count();
        $pendinglinks = $backlinks->where("status", "Pending")->count();
        $declinedlinks = $backlinks->where("status", "Declined")->count();

        $data_name = ['Active', 'Inactive', 'Pending', 'Declined'];
        $pie_data = [
            ["name" => "Active", "value" => $activelinks],
            ["name" => "Inactive", "value" => $inactivelinks],
            ["name" => "Pending", "value" => $pendinglinks],
            ["name" => "Declined", "value" => $declinedlinks],
        ];
    
        $values = [];
        foreach ($backlinks as $data) {
            $values[] = [
                "name" => $data->website,
                "data" => [
                    "url" => $data->website,
                    "da" => $data->domain_authority,
                    "pa" => $data->page_authority
                ]
            ];
        }
    
        $domain_authority = $backlinks->sum("domain_authority");
        $page_authority = $backlinks->sum("page_authority");
        $users = User::whereIn('id', $backlinks->pluck('user_id')->unique())->get();
        return view('backlinks.list', compact([
            'backlinks', 'pie_data', 'data_name', 'values', 'domain_authority', 'page_authority', 'users',
            'request', 'activelinks', 'inactivelinks', 'pendinglinks', 'declinedlinks', 'totallinks'
        ]));
    }
    
    
    // public function index()
    // {
    //    if (auth()->user()->hasRole(['Admin', 'Super Admin'])) {
    //        $backlinks = Backlinks::get();
    //    } else {
    //     $backlinks = Backlinks::where('user_id', auth()->user()->id)->where('website_id', auth()->user()->website_id)->get();
    //    }
    //    $activelinks = $backlinks->where("status", "Active")->count();
    //    $inactivelinks = $backlinks->where("status", "Inactive")->count();
    //    $pendinglinks = $backlinks->where("status", "Pending")->count();
    //    $declinedlinks = $backlinks->where("status", "Declined")->count();
    
    //    $pie_data = [];
    //    $data_name = ['Active', 'Inactive', 'Pending', 'Declined'];
    //    $pie_data[] = ["name" => "Active", "value" => $activelinks];
    //    $pie_data[] = ["name" => "Inactive", "value" => $inactivelinks];
    //    $pie_data[] = ["name" => "Pending", "value" => $pendinglinks];
    //    $pie_data[] = ["name" => "Declined", "value" => $declinedlinks];
    
    //    $values = [];
    //    foreach ($backlinks as $data) {
    //        $values[] = [
    //            "name" => $data->website,
    //            "data" => [
    //                "url" => $data->website,
    //                "da" => $data->domain_authority,
    //                "pa" => $data->page_authority
    //            ]
    //        ];
    //    }
    
    //    $domain_authority = $backlinks->sum("domain_authority");
    //    $page_authority = $backlinks->sum("page_authority");
    
    //    return view('backlinks.list', compact([
    //        'backlinks', 'pie_data', 'data_name', 'values', 'domain_authority', 'page_authority'
    //    ]));
    // }

    public function storeOrUpdate(Request $request,  $id = null)
    {   if($request->method() == 'GET'){
            try {
                $websites = Website::all();
                $backlink = '';
                if($id) {
                    $backlink = Backlinks::findOrFail($id);
                }
                return view('backlinks.create-update', compact('websites', 'backlink'));
            } catch (\Exception $e) {
                return redirect()->route('backlinks.index')->with(['status' => 'danger', 'message' => 'An error occurred while fetching the data!']);
            }
        }elseif($request->method() == 'POST'){
            $rules = [
                'website' => 'required|exists:websites,id',
                'website' => 'required|string',
                'url' => 'required|string',
                'target_keyword' => 'required|string',
                'backlink_source' => 'required|string',
                'link_type' => 'required|in:Guest Post,Infographics,Sponsored Content',
                'anchor_text' => 'required|string',
                'domain_authority' => 'required|string',
                'page_authority' => 'required|string',
                'contact_person' => 'required|string',
                'notes_comments' => 'required|string',
                'status' => 'required',
            ];
            $validatedData = $request->validate($rules);
            try {
                if ($id) {
                    $backlink = Backlinks::findOrFail($id);
                    $backlink->update($validatedData);
                    $message = 'Backlink updated successfully!';
                } else {
                    $validatedData['website_id'] = auth()->user()->website_id;
                    $backlink->user_id = auth()->user()->id;
                    $backlink = Backlinks::create($validatedData);
                    $message = 'Backlink created successfully!';
                }       
                return redirect()->route('backlinks.index')->with(['status' => 'success', 'message'=> $message]);
            }catch(\Exception $e){
                return redirect()->route('backlinks.index')->with(['status' => 'danger', 'message' => 'An error occurred while fetching the data!']);
            }
        }
    }

    public function destroy(Request $request,  $id = null)
    {
        if($id) {
            $backlink = Backlinks::find($id);
            if($backlink){
                $backlink->delete();
                return redirect()->route('backlinks.index')->with(['status' => 'success', 'message'=> 'Backlink has been deleted!']);
            }else{
                return redirect()->route('backlinks.index')->with(['status' => 'danger', 'message'=> 'Backlink not found!']);
            }   
        }
    }

}