<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use App\Http\Requests;
use Yajra\Datatables\Datatables;
use App\User;
use App\History;
use Auth;
use Hash;

class UserController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
			$this->middleware(function ($request, $next){
				if(Auth::check()&&(Auth::user()->access=='Administrator'))
					$this->access = array("show", "edit", "create", "delete", "showuser", "passworduser");
				else if(Auth::check()&&(Auth::user()->access=='PPNAdmin'||Auth::user()->access=='NonPPNAdmin'))
					$this->access = array("showuser", "passworduser");
				else
					$this->access = array("");
			return $next($request);
			});
			
      date_default_timezone_set('Asia/Krasnoyarsk');
      $this->date = date("H:i:s \G\M\T m/d/Y");
    }
		
		public function Users(Request $request){
			if(in_array("showuser", $this->access)){
				if (isset($request->id)){
					$user_detail = User::where('id', $request->id)->first();
					return $user_detail->toJson();
				}else{
					$users = User::all();
					return view('pages.user.user')
					->with('url', 'user')
					->with('users', $users)
					->with('top_menu_sel', 'user_view')
					->with('page_title', 'User')
					->with('page_description', 'Index');
				}
			}else
				return redirect()->back();
		}
		
		public function AddUsers(){
			if(in_array("create", $this->access)){
				return view('pages.user.adduser')
				->with('url', 'user')
				->with('top_menu_sel', 'user_add')
				->with('page_title', 'Add User')
				->with('page_description', 'Create');
			}else
				return redirect()->back();
		}

		public function NewUsers(Request $request){
			//Validation
			$this->validate($request, [
				'name'=>'required',
				'email'=>'required|unique:users',
				'password' => 'required|confirmed',
				'password_confirmation' => 'required',
				'access'=>'required'
			], [
				'name.required' => 'The Name field is required.',
				'email.required' => 'The User Name field is required.',
				'email.unique' => 'The User Name has already been taken.',
				'password.required' => 'The Password field is required.',
				'password_confirmation.required' => 'The Confirm Password field is required.',
				'access.required' => 'The Access field is required.'
			]);
			$last_id = User::max('id') + 1;
			$history = new History;
			$history->User = Auth::user()->name;
			$history->History = 'ADD User WITH ID '.$last_id.', NAME '.$request->name.', EMAIL '.$request->email.', PASSWORD '.bcrypt($request->password).', ACCESS '.$request->access;
			$history->save();
			$user = new User;
			$user->id = $last_id;
			$user->name = $request->name;
			$user->email = $request->email;
			$user->password = bcrypt($request->password);
			$user->access = $request->access;
			$user -> save();
			return redirect('/user')
			->with('message', 'User '.$user->name.' (id:'.$user->id.') has been successfully added.')
			->with('id', $user->id);
		}
		
		public function DeleteUsers(Request $request){
			if(in_array("delete", $this->access)){
				$this->validate($request, [
					'id'=>'required'
				]);
				$user = User::find($request->id);
				$history = new History;
				$history->User = Auth::user()->name;
				$history->History = 'DELETE User REMOVE ID '.$user->id.', NAME '.$user->name.', EMAIL '.$user->email.', PASSWORD '.$user->password.', ACCESS '.$user->access;
				$history->save();
				$user->delete();
				DB::statement('ALTER TABLE users AUTO_INCREMENT = 1');
				$request->session()->flash('message', 'User '.$user->name.' (ID: '.$user->id.') is successfully deleted');
			}else
				return redirect()->back();
		}
		
		public function PasswordUsers(Request $request){
			if(in_array("passworduser", $this->access)){
				//Validation
				$this->validate($request, [
					'id'=>'required',
					'password'=>'required',
					'cpassword'=>'required',
					'opassword'=>'required'
				], [
					'password.required' => 'The New Password field is required.',
					'cpassword.required' => 'The Confirm Password field is required.',
					'opassword.required' => 'The Old Password field is required.'
				]);
				$user = User::find($request->id);
				if($request->password!=$request->cpassword){
					$request->session()->flash('error', 'The password confirmation does not match.');
					$request->session()->flash('id', $user->id);
				}else if(Hash::check($request->opassword, $user->password)){
					$history = new History;
					$history->User = Auth::user()->name;
					$history->History = 'EDIT User PASSWORD FROM ID '.$user->id.', NAME '.$user->name.', EMAIL '.$user->email.', ACCESS '.$user->access;
					$history->save();
					$user->password = bcrypt($request->password);
					$user -> save();
					$request->session()->flash('message', 'User '.$user->name.' (ID: '.$user->id.') password has been changed successfully');
					$request->session()->flash('id', $user->id);
				}else{
					$request->session()->flash('error', 'User '.$user->name.' (ID: '.$user->id.') input wrong old password ');
					$request->session()->flash('id', $user->id);
				}
			}else
				return redirect()->back();
		}
		
		public function EditUsers(Request $request){
			if(in_array("edit", $this->access)){
				//Validation
				$this->validate($request, [
					'name'=>'required',
					'email'=>'required|unique:users,email,'.$request->oldemail.',email',
					'access'=>'required'
				], [
					'name.required' => 'The Name field is required.',
					'email.required' => 'The User Name field is required.',
					'email.unique' => 'The User Name has already been taken.',
					'access.required' => 'The Access field is required.'
				]);
				$user = User::find($request->id);
				$history = new History;
				$history->User = Auth::user()->name;
				$history->History = 'EDIT User FROM ID '.$user->id.', NAME '.$user->name.', EMAIL '.$user->email.', ACCESS '.$user->access.' INTO ID '.$request->id.', NAME '.$request->name.', EMAIL '.$request->email.', ACCESS '.$request->access;
				$history->save();
				$user->name = $request->name;
				$user->email = $request->email;
				$user->access = $request->access;
				$user -> save();
				$request->session()->flash('message', 'User '.$user->name.' (ID: '.$user->id.') has been successfully edited');
				$request->session()->flash('id', $user->id);
			}else
				return redirect()->back();
		}
}

?>