<?php

namespace App\Http\Controllers\BackgroundManagement;

use App\AppResponse;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{

    public function index(Request $request){
        $title1 = '首页';
        $title2 = '后台管理页面';
        $titleLink1 = '/';
        $titleLink2 = '#/';

        return view('admin', compact('title1','title2','titleLink1','titleLink2'));
    }

}