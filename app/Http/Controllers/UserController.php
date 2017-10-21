<?php

namespace App\Http\Controllers;

use App\Species;
use Auth;
use DB;
use App\Roles;
use App\User;
use App\Category;
use App\Usergroup;
use App\Staff;
use Hash;
use App\Reportblock;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use App\Events\UserAllNotification;

class UserController extends Controller
{


    public function __construct()
    {

    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::with('species')->where('is_deleted','false')->where('suspend','0')->orderBy('users.id', 'desc')->get();
        return view('admin.user.index',compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $usergroups = Usergroup::all();
        $userroles = Roles::orderBy('id', 'desc')->get();
        $species = Species::orderBy('id', 'asc')->get();
		$categories = Category::all();
        return view('admin.user.create', compact(['usergroups', 'userroles','species','categories']));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->validate($request, [
       'name' => 'required|max:255|unique:users',
       'displayname' => 'required|max:255|unique:users',
       'email' => 'required|email|unique:users',
       'age' => 'required|max:255',
       'password' => 'required|min:6',
       'about_me' => 'required'
      ]);
	  if($_POST['user_type']==3){
		   $this->validate($request, [
		  'category_id' => 'required'
		   ]);
	  }
        $user = new User ;
        $user->name = request('name');
        $user->displayname = request('displayname');
        $user->email = request('email');
        $user->group = request('user_group');
	
		if( $request->input('category_id') ){
            $user->category_id = $request->input('category_id');
        }
			if( $request->input('designation') ){
		$user->designation = $request->input('designation');
			}
        $user->age = request('age');
        $user->verified = 1;
        $user->password = Hash::make(request('password'));
        $user->role_id = request('user_type');
		  if( $request->input('gender') ){
            $user->gender = $request->input('gender');
        }
        $user->species_id = request('species_id');
        $user->about_me = request('about_me');
        $user->save();
        return redirect('admin/users');

        //dd($request);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user = User::find($id);
        $usergroups = Usergroup::all();
        $userroles = Roles::orderBy('id', 'desc')->get();
        $species = Species::orderBy('id', 'asc')->get();
		$categories = Category::all();
        return view('admin.user.edit',compact('user', 'usergroups', 'userroles','species','categories'));
    }



    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if($request->has('password') && $request->get('password')!="")
        {

        }
        else {
            $request->request->remove('password');
        }

        $request->validate([
            'name' => 'required|unique:users,name,'.$id,
            'displayname' => 'required|unique:users,displayname,'.$id,
            'email' => 'required|email|unique:users,email,'.$id,
            'password' => 'sometimes|min:6',
        ]);
        $data = User::find($id);
        $data->name = $request->input('name');
        $data->displayname = $request->input('displayname');
        $data->email = $request->input('email');

        if($request->has('password') && $request->get('password')!="")
        {
            $data->password = Hash::make ($request->input('password'));
        }
		$data->role_id = request('user_type');
		if( $request->input('category_id') ){
            $data->category_id = $request->input('category_id');
        }
		if( $request->input('designation') ){
		$data->designation = $request->input('designation');
			}
        $data->group = $request->input('user_group');
        if( $request->input('gender') ){
            $data->gender = $request->input('gender');
        }
        $data->age = $request->input('age');
        $data->species_id = $request->input('species_id');
        $data->about_me = $request->input('about_me');
        $data->save();
        return back()
            ->with('success','User updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

      public function profile()
    {
        $user = Auth::user();
        return view('profile.changepicture',compact('user',$user));
    }

    public function update_avatar(Request $request){

        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $user = Auth::user();

        $avatarName = $user->id.'_avatar'.time().'.'.request()->avatar->getClientOriginalExtension();

        $request->avatar->storeAs('uploads',$avatarName);

        $user->profile_pic = $avatarName;
        $user->save();

        return back()
            ->with('success','You have successfully upload image.');

    }

    //delete user function
    public function delete($id)
    {
        $user = User::find($id);
        $user->delete();
        return redirect('admin/users');
    }

    //Display Reports User
    public function reportUserDisplay()
    {
        $reportusers = Reportblock::where('type','report')->orderBy('status','asc')->orderBy('id','desc')->get();
        return view('admin.user.report', compact('reportusers'));
    }

    //Display Blocked User
    public function blockUserDisplay()
    {
        $blockusers = Reportblock::where('type','block')->get();
        return view('admin.user.block', compact('blockusers'));
    }


		public function changeReportStatus(Request $request,$id)
		{
        $reportusers = Reportblock::findorfail($id);
        $status=$request->get('report_status');

        if($status)
        {
            $report_user_input['status']=0;
            $msg="Status changed as incomplete successfully.";
        }
        else
        {
            $report_user_input['status']=1;
            $msg="Status changed as complete successfully.";
        }

        // update into db
        $reportusers->fill($report_user_input)->save();

        return back()
            ->with('success',$msg);
		}

		public function destroyUserReport($id)
		{

        $reportusers = Reportblock::findorfail($id);
        $reportusers->delete();

        return back()
            ->with('success', 'User report deleted successfully.');
		}


        public function destroyUserBlock($id){
            $reportusers = Reportblock::findorfail($id);
            $reportusers->delete();

            return back()
            ->with('success', 'User block deleted successfully.');
        }

		//view employee function
		public function employee()
		{
			  $staffs = DB::table('users')->join('staff', 'users.id', '=', 'staff.staff_id')->select('users.name as staffname', 'staff.*')->get();
			 // echo"<pre>";  print_r($staffs); die ;
			  return view('admin.user.employee',compact('staffs'));
		}
		//create employee function

		public function createemployee()
		{
			$staff=Auth::user()->where('role_id','3')->get();
			return view('admin.user.createemployee', compact('staff'));
		}
		//store employee function

		public function storeemployee(Request $request)
		{

			$staff = new Staff ;
			$staff->title = $request->input('title');
			$staff->description = $request->input('description');
			$staff->staff_id = $request->input('employee_name');
			$staff->save();
			return redirect('admin/users/employee')->with('success','Employee of the month  successfully added');

		}
		//  edit employee function
			public function editemployee($id)
		{

			$jobs = Staff::find($id);
			$staff=Auth::user()->where('role_id','3')->get();
			return view('admin.user.editemployee',compact( 'jobs', 'staff'));
		}

		// update employee function
	   public function updateemployee(Request $request, $id)
		{


			$data = Staff::find($id);
			$data->title = $request->input('title');
			$data->description = $request->input('description');
			$data->staff_id = $request->input('employee_name');
			$data->save();
			return redirect('admin/users/employee')->with('success',' updated successfully');
		}

		//delete employee function
		public function deleteemployee($id)
		{
			$user = Staff::find($id);
			$user->delete();
		    return back()
				->with('success','Successfully Deleted');
		}

        //delete employee function
        public function removeProfile($id)
        {
            $user= User::findorfail($id);
            $user->profile_pic="";
            $user->photo_status=1;
            $user->save();
            return back()
            ->with('message',$user->name.' Profile Removed Sucessfully');
        }

        public function getuserformass()
        {
            $users = User::with('species')->where('is_deleted','false')->where('suspend','0')->orderBy('users.id', 'desc')->get();
            return view('admin.user.massmessage', compact('users'));
        }

        public function storeuserformass(Request $request)
        {
            $selected_user_ids = $request->input('multiple_usr_checkboxes');
            $message = $request->input('user_message');

            $admin=Auth::user();
            $admin_id = $admin->id;

            if($selected_user_ids && $message){
                foreach($selected_user_ids as $key=>$value)
                {
                    $userdata = User::find($value);                    
                    $user_email = $userdata->user_email;

                    if(!$user_email){
                        $emaildata = array();
                    }else{
                        $emaildata = array(
                            'email' => $userdata->user_email,
                            'displayname' => $userdata->displayname,
                            'email_message' => $message
                        );
                    }

                    $notficationdata = array(
                        'user_id' => $userdata->id,
                        'message' => $message,
                        'type' => 'mass_message',
                        'created_by' => $admin_id
                    );
                    
                    if(!$userdata->uuid){
                        $sldata = array();
                    }else{
                        $sldata = array(
                            'uuid' => $userdata->uuid,
                            'message' => $message,
                            'type' => 'mass_message'
                        );
                    }

                    $messagedata = array(
                        'user_id' => $admin_id,
                        'reciever_id' => $userdata->id,
                        'message' => $message,
                        'type' => 'message_notification'
                    );
                    $allnoticationdata = array(
                        'emailtype' =>$emaildata,
                        'messagetype' =>$messagedata,
                        'notificationtype' =>$notficationdata,
                        'sl_notificationtype' =>$sldata
                    );
                    \Event::fire(new UserAllNotification($allnoticationdata));
                }
                return redirect('admin/users/massmessage')->with('success','Message send successfully');
            }else{
                return back()->with('error', 'Please select users')->withInput($request->all());
            }
        }

}
