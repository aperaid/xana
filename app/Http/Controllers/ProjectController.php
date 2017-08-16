<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Project;
use App\Reference;
use App\Penawaran;
use App\History;
use Session;
use DB;
use Auth;

class ProjectController extends Controller
{
	public function __construct()
	{
		$this->middleware(function ($request, $next){
			if(Auth::check()&&(Auth::user()->access=='Administrator'||Auth::user()->access=='PPNAdmin'||Auth::user()->access=='NonPPNAdmin'||Auth::user()->access=='Purchasing'||Auth::user()->access=='SuperPurchasing'))
				$this->access = array("index", "create", "show", "edit");
			else
				$this->access = array("");
    return $next($request);
    });
	}
	
	public function index(){
		if(Auth::user()->access == 'Administrator'){
			$project = Project::all();
		}else if(Auth::user()->access == 'PPNAdmin'){
			$project = Project::select('customer.CCode', 'project.*')
			->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
			->where('PPN', 1)
			->get();
		}else if(Auth::user()->access == 'NonPPNAdmin'){
			$project = Project::select('customer.CCode', 'project.*')
			->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
			->where('PPN', 0)
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

	public function create(){
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

	public function store(Request $request){
		//Validation
		$this->validate($request, [
			'PCode' => 'required|unique:project',
			'Project'=>'required',
			'Sales'=>'required',
			'CCode'=>'required'
		], [
			'PCode.required' => 'The Project Code field is required.',
			'PCode.unique' => 'The Project Code has already been taken.',
			'Project.required' => 'The Project Name field is required.',
			'Sales.required' => 'The Sales field is required.',
			'CCode.required' => 'The Company Code field is required.',
		]);
		
		$inputs = $request->all();
		$project = Project::Create($inputs);
	
		$history = new History;
		$history->User = Auth::user()->name;
		$history->History = 'Create Project on PCode '.$request['PCode'];
		$history->save();
		
		return redirect()->route('project.show', $request->id);
	}

	public function show($id){
		$project = Project::select('project.id as proid', 'project.*', 'customer.*')
		->leftJoin('customer', 'project.CCode', '=', 'customer.CCode')
		->where('project.id', $id)
		->first();
		
		$checkproj = Reference::where('PCode', $project->PCode)->count();
			if($checkproj==0)
				$checkproj = 0;
			else
				$checkproj = 1;
			
		echo $project->PCode;
			
		$checkpen = Penawaran::where('PCode', $project->PCode)->count();
			if($checkpen==0)
				$checkpen = 0;
			else
				$checkpen = 1;

		if(in_array("show", $this->access)){
			return view('pages.project.show')
			->with('url', 'project')
			->with('project', $project)
			->with('checkproj', $checkproj)
			->with('checkpen', $checkpen)
			->with('top_menu_sel', 'menu_project')
			->with('page_title', 'Project')
			->with('page_description', 'View');
		}else
			return redirect()->back();
	}

	public function edit($id){
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

	public function update(Request $request, $id){
		//Validation
		$this->validate($request, [
			'PCode' => 'required|unique:project,PCode,'.$request->PCode.',PCode',
			'Project'=>'required',
			'Sales'=>'required',
			'CCode'=>'required'
		], [
			'PCode.required' => 'The Project Code field is required.',
			'PCode.unique' => 'The Project Code has already been taken.',
			'Project.required' => 'The Project Name field is required.',
			'Sales.required' => 'The Sales field is required.',
			'CCode.required' => 'The Company Code field is required.',
		]);
		
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

	public function DeleteProject(Request $request){
		Project::destroy($request->id);
		DB::statement('ALTER TABLE project auto_increment = 1;');
		
		$history = new History;
		$history->User = Auth::user()->name;
		$history->History = 'Delete Project on PCode '.$request->PCode;
		$history->save();
		
		Session::flash('message', 'Project with PCode '.$request->PCode.' is deleted');
	}
}
