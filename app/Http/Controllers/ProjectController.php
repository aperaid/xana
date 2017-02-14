<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Project;
use App\Reference;
use App\History;
use Session;
use DB;
use Auth;

class ProjectController extends Controller
{
	public function __construct()
	{
		$this->middleware(function ($request, $next){
			if(Auth::check()&&(Auth::user()->access=='Admin'||Auth::user()->access=='CUSTINVPPN'||Auth::user()->access=='CUSTINVNONPPN'))
				$this->access = array("index", "create", "show", "edit");
			else
				$this->access = array("");
			
		if(Auth::user()->access=='POINVPPN'||Auth::user()->access=='CUSTINVPPN')
				$this->PPNNONPPN = 1;
			else if(Auth::user()->access=='POINVNONPPN'||Auth::user()->access=='CUSTINVNONPPN')
				$this->PPNNONPPN = 0;
    return $next($request);
    });
	}
	
	public function index()
	{
		if(Auth::user()->access == 'Admin'){
			$project = Project::all();
		}else{
			$project = Project::leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
			->where('PPN', $this->PPNNONPPN)
			->get();
		}

	if(in_array("index", $this->access)){
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
		if(in_array("create", $this->access)){
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
		
	$checkproj = Reference::where('PCode', $project->PCode)->count();
		if($checkproj==0)
			$checkproj = 0;
		else
			$checkproj = 1;

		if(in_array("show", $this->access)){
			return view('pages.project.show')
			->with('url', 'project')
			->with('project', $project)
			->with('checkproj', $checkproj)
			->with('top_menu_sel', 'menu_project')
			->with('page_title', 'Project')
			->with('page_description', 'View');
		}else
			return redirect()->back();
	}

	public function edit($id)
	{
		$project = Project::find($id);

		if(in_array("edit", $this->access)){
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
		$project->Sales = $request->Sales;
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
		DB::statement('ALTER TABLE project auto_increment = 1;');
		
		$history = new History;
		$history->User = Auth::user()->name;
		$history->History = 'Delete Project on PCode '.$request['PCode'];
		$history->save();
		
		Session::flash('message', 'Delete is successful!');

		return redirect()->route('project.index');
	}
}
