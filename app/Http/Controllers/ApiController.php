<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use App\User;
use App\profile;
use App\availability;
use Carbon\Carbon;

class ApiController extends Controller {

    public function mainApi(Request $request) {
        $update = $request->json()->all();
        if (parent::getMainAccount($request)) {
            $action = $update['queryResult']['action'];

            if ($action == 'checkAvailability') {
                return self::checkAvailability($update, $request);
            } else {
                return parent::error('Sorry! Wrong Action!', $update["responseId"]);
            }
        } else {
            return parent::error('Please check headers!', $update["responseId"]);
        }
    }

    public static function checkAvailability($update, $request) {
        $user_id = $request->header('account');

        $date = $update['queryResult']['parameters']['date'];
        $time = $update['queryResult']['parameters']['time'];
        $servicePerson = $update['queryResult']['parameters']['servicePerson'];
        if ($date == '' || $time == ''):
            return parent::error('Date and Time are required both!', $update["responseId"]);
        endif;
        $date = date("Y-m-d", strtotime($date));
        $time = date("g:i:s", strtotime($time));
        $neededTime = date("g:i A", strtotime($time));
        //dd($time);
        if ($servicePerson == '' || $servicePerson == null) {
            //check on first come first serve
            $getAllProfiles = profile::where('user_id', $user_id)->get();
            if (count($getAllProfiles) > 0):
                foreach ($getAllProfiles as $profile) {
                    $checkAvailablity = availability::where('profile_id', $profile->id)->where('start_date', '<=', $date)->where('end_date', '>=', $date)->
                            where('start_time', '<=', $time)
                            ->where('end_time', '>=', $time)
                            ->first();
                    if ($checkAvailablity) {
                        $getProfile = profile::where('id', $checkAvailablity->profile_id)->first();
                        return parent::success("" . $getProfile->name . " is available at " . $neededTime . ", go ahead with booking?", $update["responseId"]);
                    }
                }
                return parent::error('No Available Profiles Found', $update["responseId"]);
            else:
                return parent::error('No Profiles found for this account!', $update["responseId"]);
            endif;
        } else {

            //Check only for service person
            $profile = profile::where('name', $servicePerson)->where('user_id', $user_id)->first();
            if ($profile) {

                $checkAvailablity = availability::where('profile_id', $profile->id)->where('start_date', '<=', $date)->where('end_date', '>=', $date)->
                        where('start_time', '<=', $time)
                        ->where('end_time', '>=', $time)
                        ->first();
                $getProfile = profile::where('id', $checkAvailablity->profile_id)->first();
                if ($checkAvailablity) {
                    return parent::success("" . $getProfile->name . " is available at " . $neededTime . ", go ahead with booking?", $update["responseId"]);
                } else {
                    return parent::error("Sorry " . $getProfile->name . " is not available at " . $neededTime . ".", $update["responseId"]);
                }
            } else {
                return parent::error("Sorry Profile " . $servicePerson . " is not found.", $update["responseId"]);
            }
        }

        return parent::success("Success IN", $update["responseId"]);
    }

}
