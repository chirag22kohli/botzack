<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use App\User;

class Controller extends BaseController {

    use AuthorizesRequests,
        DispatchesJobs,
        ValidatesRequests;

    public static function getMainAccount($request) {
        $header = $request->header('account');
        if ($header) {
            $checkUser = User::where('id', $header)->first();

            if ($checkUser):
                return true;
            else:
                return false;
            endif;
        }else {
            return false;
        }
    }

    public static function success($message, $responseId, $session, $customContext = "", $contextArray = []) {
        if(empty($contextArray)):
            $contextArray = (object)[];
        endif;
        $sessionCustom = explode('contexts/', $session);
        $data = array(
            "source" => $responseId,
            "fulfillmentText" => $message,
            "payload" => array(
                "items" => [
                    array(
                        "simpleResponse" =>
                        array(
                            "textToSpeech" => "Success"
                        )
                    )
                ],
            ),
            "outputContexts" => [[
            "name" => $session,
            "lifespanCount" => 1,
            "parameters" => [
                "dateCustom" => '27-11-2018'
            ]
                ],
                [
                    "name" => $customContext,
                    "lifespanCount" => 1,
                    "parameters" => $contextArray
                ]
            ],
        );


        return response()->json($data, "200");
    }

    public static function successWithoutContext($message, $responseId) {

        $data = array(
            "source" => $responseId,
            "fulfillmentText" => $message,
            "payload" => array(
                "items" => [
                    array(
                        "simpleResponse" =>
                        array(
                            "textToSpeech" => "Success"
                        )
                    )
                ],
            ),
        );


        return response()->json($data, "200");
    }

    public static function error($message, $responseId) {
        $data = array(
            "source" => $responseId,
            "fulfillmentText" => $message,
            "payload" => array(
                "items" => [
                    array(
                        "simpleResponse" =>
                        array(
                            "textToSpeech" => "Bad request"
                        )
                    )
                ],
            ),
        );


        return response()->json($data, "200");
    }

}
