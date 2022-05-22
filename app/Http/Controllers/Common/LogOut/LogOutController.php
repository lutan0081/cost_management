<?php

namespace App\Http\Controllers\Common\logOut;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\Log;

use App\Http\Controllers\Controller;

class LogOutController extends Controller
{   
    // ログアウト
    public function logOut(Request $request){

        $request->session()->flush();

        return redirect('/');
    }
}