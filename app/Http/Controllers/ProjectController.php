<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Project;
use App\History;
use Session;
use DB;
use Auth;

class ProjectController extends Controller
{
    public function index()
    {
		$project = Project::all();

    if(Auth::check()&&Auth::user()->access()=='Admin'){
    	return view('pages.project.indexs')
      ->with('url', 'project')
      ->with('project', $project)
      ->with('top_menu_sel', 'menu_project')
      ->with('page_title', 'Project')
      ->with('page_description', 'Index');
    }else
      return redirect()->back();
    
    }

    public function create()
    {
      $project = Project::select([
        DB::raw('MAX(project.id) AS maxid')
      ])
      ->first();
      if(Auth::check()&&Auth::user()->access()=='Admin'){
        return view('pages.project.create')
        ->with('url', 'project')
        ->with('project', $project)
        ->with('top_menu_sel', 'menu_project')
        ->with('page_title', 'Project')
        ->with('page_description', 'Create');
      }else
        return redirect()->back();
    }

    public function store(Request $request)
    {
    	
    	$inputs = $request->all();

      $is_exist = Project::where('PCode', $request->PCode)->first();
      if(isset($is_exist->PCode)){
        return redirect()->route('project.create')->with('error', 'Project with PCode '.strtoupper($request->PCode).' is already exist!');
      }else{
        $project = Project::Create($inputs);
      }
      
      $history = new History;
      $history->User = Auth::user()->name;
      $history->History = 'Create Project on PCode '.$request['PCode'];
      $history->save();

    	return redirect()->route('project.index');
    }

    public function show($id)
    {
    	$project = Project::leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
      ->select('project.id as proid', 'project.*', 'customer.*')
      ->where('project.id', $id)
      ->first();

      if(Auth::check()&&Auth::user()->access()=='Admin'){
        return view('pages.project.show')
        ->with('url', 'project')
        ->with('project', $project)
        ->with('top_menu_sel', 'menu_project')
        ->with('page_title', 'Project')
        ->with('page_description', 'View');
      }else
        return redirect()->back();
    }

    public function edit($id)
    {
    	$project = Project::find($id);

      if(Auth::check()&&Auth::user()->access()=='Admin'){
        return view('pages.project.edit')
        ->with('url', 'project')
        ->with('project', $project)
        ->with('top_menu_sel', 'menu_project')
        ->with('page_title', 'Project')
        ->with('page_description', 'Edit');
      }else
        return redirect()->back();
    }

    public function update(Request $request, $id)
    {
    	$project = Project::find($id);

    	$project->PCode = $request->PCode;
    	$project->Project = $request->Project;
      $project->ProjAlamat = $request->ProjAlamat;
      $project->ProjZip = $request->ProjZip;
      $project->ProjKota = $request->ProjKota;
    	$project->CCode = $request->CCode;
    	$project->save();

      $history = new History;
      $history->User = Auth::user()->name;
      $history->History = 'Update Project on PCode '.$request['PCode'];
      $history->save();
      
    	return redirect()->route('project.show', $id);
    }

    public function destroy(Request $request, $id)
    {
    	Project::destroy($id);
      
      $history = new History;
      $history->User = Auth::user()->name;
      $history->History = 'Delete Project on PCode '.$request['PCode'];
      $history->save();
      
      Session::flash('message', 'Delete is successful!');

    	return redirect()->route('project.index');
    }
}
