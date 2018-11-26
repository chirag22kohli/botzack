<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class ApiController extends Controller {

    public function checkAvailablity(Request $request) {
        $update = $request->json()->all();
        if(parent::getMainAccount($request)) {  
            return parent::success("Success IN",$update["responseId"]);
        }else{
          return  parent::error('Please check headers!',$update["responseId"]);
        }
    }

}
