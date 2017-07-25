<?php

namespace App\Http\Controllers;

use App\Advice;
use App\AppResponse;
use App\User;
use Illuminate\Http\Request;

class AdvicesController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
    }

    public function index(){
        $title1 = '合理化建议';
        $title2 = null;

        if(auth()->user()->id == 1){
            $titleLink1 = '/advices#/suggester/index';
        }else{
            $titleLink1 = '/advices#/rater/index';
        }
        $titleLink2 = null;
        
        return view('advices', compact('title1','title2','titleLink1','titleLink2'));
    }

    public function store(Request $request){
//        \Log::info($request->all());
        $validator = \Validator::make($request->all(), [
            'title' => "required|max:25",
            'content' => "required",
        ], [
            'title.required' => "标题必须填写！",
            'title.max' => "标题过长！",
            'content.required' => "建议内容必须填写！"
        ], []);
        if ($validator->fails()){
            return AppResponse::result(false, $validator->messages());
        }
        $rater = User::where('is_senior_manager', 1)->first();
//        \Log::info($rater);
        $tempAdvice = [];
        $tempAdvice['title'] = $request->get('title');
        $tempAdvice['content'] = $request->get('content');
        $tempAdvice['suggest_id'] = auth()->user()->id;
        $tempAdvice['rater_id'] = $rater->id;
        $advice = Advice::create($tempAdvice);
    }

    //获取所有的本人的合理化建议
    public function getAllAuthAdvices(){
        $advices = Advice::where('suggest_id', auth()->user()->id)->orderBy("created_at", "DESC")->get();
        return $advices;
    }

    public function show($id){
        $advice = Advice::find($id);
        return $advice;
    }

    //评审人编辑时获取advice详情
    public function raterEditGetAdviceDetail ($id){
        $adviceDetail = Advice::select([
            'advices.id',
            'advices.title',
            'advices.content',
            'advices.created_at',
            'advices.is_processed',
            'advices.is_accept',
            'advices.score',
            'advices.comment',
            'advices.suggest_id',
            'users.name'
        ])->leftJoin("users", "users.id", "=", "advices.suggest_id")
            ->where("advices.id", "=", $id)
            ->get();
        return $adviceDetail;
    }

    public function update($id, Request $request){

        $validator = \Validator::make($request->all(), [
            'title' => "required|max:25",
            'content' => "required",
        ], [
            'title.required' => "标题必须填写！",
            'title.max' => "标题过长！",
            'content.required' => "建议内容必须填写！"
        ], []);
        if ($validator->fails()){
            return AppResponse::result(false, $validator->messages());
        }

        $advice = Advice::find($id);
        return AppResponse::result($advice->update($request->all()));
    }

    //获取所有的提建议者的建议
    public function getAllSuggesterAdvices(Request $request){
//        \Log::info($request->all());

        $advices = Advice::select([
            'advices.id',
            'advices.title',
            'advices.content',
            'advices.suggest_id',
            'users.name',
            'advices.is_processed',
            'advices.is_accept',
            'advices.score',
            'advices.created_at',
            'advices.comment'
        ])
            ->leftJoin('users', 'users.id', '=', 'advices.suggest_id');

        switch($request->get('team')){
            case "all":
                $advices = $advices;
                break;
            case "untreated":
                $advices = $advices->where('advices.is_processed', 0);
                break;
            case "Adopted":
                $advices = $advices->where('advices.is_processed', 1)->where('advices.is_accept', 1);
                break;
            case "notAdopted":
                $advices = $advices->where('advices.is_processed', 1)->where('advices.is_accept', 0);
                break;
            default:
                $advices = $advices;
        }

        if($request->has('search')){
            $idArray1 = Advice::where('advices.title', 'like', '%' . $request->get('search') . '%')->pluck('id');
            $idArray2 = Advice::leftJoin('users', 'users.id', '=', 'advices.suggest_id')
                ->where('users.name', 'like', '%' . $request->get('search') . '%')
                ->pluck('advices.id');
            $idArray = [];
            for($i = 0; $i < count($idArray2); $i++){
                array_push($idArray, $idArray2[$i]);
            }
            for($i = 0; $i < count($idArray1); $i++){
                array_push($idArray, $idArray1[$i]);
            }
            $idArray3 = array_flip($idArray);
            $idArray = array_flip($idArray3);
            $advices = $advices->whereIn("advices.id", $idArray)->get();
        }else{
           $advices = $advices->get();
        }
        return $advices;
    }
    
    //评判advice
    public function raterJudgeAdvice($id, Request $request){
//        \Log::info($id);
//        \Log::info($request->all());
        $advice = Advice::where('id', "=", $id);
        if($request->get("is_accept") == 1){
            $advice->update(['is_processed' => $request->get('is_processed'), 'is_accept' => $request->get('is_accept'), 'score' => $request->get('score'), 'comment' => $request->get('comment')]);
        }
        if($request->get("is_accept") == 0){
            $advice->update(['is_processed' => $request->get('is_processed'), 'is_accept' => $request->get('is_accept')]);
        }
    }
}
