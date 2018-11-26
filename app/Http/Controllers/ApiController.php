<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class ApiController extends Controller {

    public function mainApi(Request $request) {
        $update = $request->json()->all();
        if(parent::getMainAccount($request)) {  
            $action =  $update['queryResult']['action'];
           
            if($action == 'checkAvailability'){
              return  self::checkAvailability($update);
            }else{
                 return  parent::error('Sorry! Wrong Action!',$update["responseId"]);
            }
            
            
        }else{
          return  parent::error('Please check headers!',$update["responseId"]);
        }
    }
    
    public static function checkAvailability($update){
        return parent::success("Success IN", $update["responseId"]);
    }

}
