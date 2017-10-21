<?php

namespace App\Http\Controllers;

use DB;
use App\Questionnaires;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Usergroup;
use Session;
use App\MatchQuestCategory;

class QuestionnairesController extends Controller
{

    public function __construct()
    {

    }

    public function init()
    {
        $usergroups=Usergroup::get();
        return view('admin.questionnaires.init',compact('usergroups'));
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index( $groupId )
    {
        $usergroups=Usergroup::get();
        $usergroup=Usergroup::find($groupId);
        if ($usergroup == null)
        {
            return redirect()->route('questionnaires.init');
        }
        $questionnaires=Questionnaires::where('group_id',$groupId)->paginate(15);

        return view('admin.questionnaires.index',compact('usergroups','usergroup','questionnaires','groupId'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create( $groupId )
    {
        $match_quest_categories = MatchQuestCategory::all();
        return view('admin.questionnaires.create',compact('groupId', 'match_quest_categories'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request )
    {
        $validator = Validator::make($request->all(), [
            'user_group' => 'required|exists:usergroups,id',
            'question_title' => 'required|max:200',
            'question_type' => 'required|max:200',
            //'question' => 'required_if:question_type,==,2',
            'category' => 'required|exists:match_quest_categories,id',
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        if ($request->question_type == 2 || $request->question_type == 3 || $request->question_type == 5) {
            $flag = 0;
            if($request->has('question') && count($request->question) > 0){
                foreach($request->question['options'] as $row){
                    if(empty($row)){
                        $flag = 1;
                    }
                }
            }else{
                $flag = 1;
            }
            if($flag){
                return redirect()->back()->with('question', 'Enter ooption if you select answer type select, checkbox or multiple choice');
            }
        }
        $data = new Questionnaires;
        $data->group_id = $groupId = request('user_group');
        $data->question_title = request('question_title');
        $data->question_type = request('question_type');
        $data->question_data = json_encode( request('question') );
        $data->category_id = request('category');
        $data->save();
        return redirect("admin/questionnaires/$groupId");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Questionnaires  $questionnaires
     * @return \Illuminate\Http\Response
     */
    public function show(Questionnaires $questionnaires)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Questionnaires  $questionnaires
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $questionnaires = Questionnaires::findorfail($id);
        $usergroup=Usergroup::get();
        $match_quest_categories = MatchQuestCategory::all();
        return view('admin.questionnaires.edit',compact('questionnaires', 'usergroup', 'match_quest_categories'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Questionnaires  $questionnaires
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'user_group' => 'required|exists:usergroups,id',
            'question_title' => 'required|max:200',
            'question_type' => 'required|max:200',
            //'question' => 'required_if:question_type,==,2',
            'category' => 'required|exists:match_quest_categories,id',
        ]);
        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }
        if ($request->question_type == 2 || $request->question_type == 3 || $request->question_type == 5) {
            $flag = 0;
            if($request->has('question') && count($request->question) > 0){
                foreach($request->question['options'] as $row){
                    if(empty($row)){
                        $flag = 1;
                    }
                }
            }else{
                $flag = 1;
            }
            if($flag){
                return redirect()->back()->with('question', 'Enter ooption if you select answer type select, checkbox or multiple choice');
            }
        }
        $questionnaires = Questionnaires::findorfail($id);
        $questionnaires->group_id = request('user_group');
        $questionnaires->question_title = request('question_title');
        $questionnaires->question_type = request('question_type');
        $questionnaires->question_data = json_encode( request('question') );
        $questionnaires->category_id = request('category');
        $questionnaires->save();
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Questionnaires  $questionnaires
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
       $questionnary = Questionnaires::findorfail($id);
       $questionnary->delete();
        return redirect('/admin/questionnaires');
    }

    public function sortMatchQuests(Request $request){

       if($request->get('action') == 'action_sort_match_quests'){
           $position = 1;
           $data = $request->get("data");


           foreach($data as $id){
               DB::table("questionnaires")->
                   where("id", $id)->
                   update(array("sort" => $position));
               $position++;
           }
           Session::flash("success","Quest order updated Successfully");
       }

   }

}
