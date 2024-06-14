<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\{Backlinks,Website};
class BacklinkController extends Controller
{
    //
    public function index()
    {
        $backlinks = Backlinks::where('website_id', auth()->user()->website_id)->get();
        $activelinks = Backlinks::where("status","Active")->where('website_id', auth()->user()->website_id)->count();
        $inactivelinks = Backlinks::where("status","Inactive")->where('website_id', auth()->user()->website_id)->count();
        $pendinglinks = Backlinks::where("status","Pending")->where('website_id', auth()->user()->website_id)->count();
        $declinedlinks = Backlinks::where("status","Declined")->where('website_id', auth()->user()->website_id)->count();

        $pie_data=[];
        $data_name=['Active','Inactive','Pending','Declined'];
        array_push($pie_data,["name"=>"Active","value" =>  $activelinks]);
        array_push($pie_data,["name"=>"Inactive","value" =>  $inactivelinks]);
        array_push($pie_data,["name"=>"Pending","value" =>  $pendinglinks]);
        array_push($pie_data,["name"=>"Declined","value" =>  $declinedlinks]);

        $values=[];
        foreach($backlinks as $data){
            array_push($values,["name"=>$data->website,"data" => [
            "url" => $data->website,
            "da" => $data->domain_authority,
            "pa" => $data->page_authority
        ]
        ]);

        }
        $domain_authority = Backlinks::pluck("domain_authority")->sum();
        $page_authority = Backlinks::pluck("page_authority")->sum();
 
       
        return view('backlinks.list', compact(['backlinks', 'pie_data', 'data_name', 'values','domain_authority','page_authority']));
    }

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
                'date' => 'required|date',
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