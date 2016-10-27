<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Project;
use Session;
use DB;

class ExtendController extends Controller
{
    public function index()
    {
		$project = Project::all();

    	return view('pages.project.indexs')
      ->with('project', $project)
      ->with('top_menu_sel', 'menu_project')
      ->with('page_title', 'Project')
      ->with('page_description', 'Index');
    }

    public function create()
    {
      $project = Project::orderby('id', 'desc')
      ->first();
      
    	return view('pages.project.create')
      ->with('project', $project)
      ->with('top_menu_sel', 'menu_project')
      ->with('page_title', 'Project')
      ->with('page_description', 'Create');
    }

    public function store(Request $request)
    {
    	
    	$inputs = $request->all();

    	$project = Project::Create($inputs);

    	return redirect()->route('project.index');
    }

    public function show($id)
    {
    	$project = Project::find($id);

    	return view('pages.project.show')
      ->with('project', $project)
      ->with('top_menu_sel', 'menu_project')
      ->with('page_title', 'Project')
      ->with('page_description', 'View');
    }

    public function edit($id)
    {
    	$project = Project::find($id);

    	return view('pages.project.edit')
      ->with('project', $project)
      ->with('top_menu_sel', 'menu_project')
      ->with('page_title', 'Project')
      ->with('page_description', 'Edit');
    }

    public function update(Request $request, $id)
    {
    	$project = Project::find($id);

    	$project->PCode = $request->PCode;
    	$project->Project = $request->Project;
      $project->Alamat = $request->Alamat;
    	$project->CCode = $request->CCode;
    	$project->save();

    	return redirect()->route('project.show', $id);
    }

    public function destroy($id)
    {
    	Project::destroy($id);
      Session::flash('message', 'Delete is successful!');

    	return redirect()->route('project.index');
    }
}
