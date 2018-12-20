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

    protected $apiAccessToken;
    protected $contentType;

    public function __construct() {


        $this->apiAccessToken = env('dialogFlowToken');
        $this->contentType = env('contentType');
    }

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
        if (empty($contextArray)):
            $contextArray = (object) [];
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
            "parameters" => $contextArray
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

    public static function findKey($array, $field, $value) {
        // var_dump($array);die();
        foreach ($array as $key) {

            if (strpos($key->$field, $value) !== false) {
                return $key->id;
            }
        }
        return false;
    }

    public static function getAllEntities($url, $type) {
        return self::getCurlResponse($url, $type);
    }

    public static function getCurlResponse($url, $type = "POST", $data = []) {

        $headers = [
            env('dialogFlowToken'),
            env('contentType'),
        ];


        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $type);
        if ($type == 'POST' || $type == "PUT") {

            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        }
        //
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
// In real life you should use something like:
// curl_setopt($ch, CURLOPT_POSTFIELDS, 
//          http_build_query(array('postvar1' => 'value1')));
// Receive server response ...
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $server_output = curl_exec($ch);

        curl_close($ch);
        return $server_output;
    }

    public static function uploadEntityData($dataArray, $entityName) {
        $dataArray['name'] = $entityName;
        $dataArray = json_encode($dataArray);
        return $putEntity = self::getCurlResponse("https://api.dialogflow.com/v1/entities?v=20150910&lang=en", "PUT", $dataArray);
    }

    public function uploadFile($request, $fileName, $path) {
        $image = $request->file($fileName);
        //dd($_FILES);
        $input['imagename'] = time() . '.' . $image->getClientOriginalExtension();
        $destinationPath = public_path($path);
        //
     
        $image->move($destinationPath, $input['imagename']);
        return $path . '/' . $input['imagename'];
    }

}
