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
use App\booking;

class ApiController extends Controller {

    public function mainApi(Request $request) {
        $update = $request->json()->all();
        if (parent::getMainAccount($request)) {

            if (!isset($update['queryResult']['action'])) {
                return parent::error('Please check headers!', $update["responseId"]);
            }
            $action = $update['queryResult']['action'];

            if ($action == 'checkAvailability') {
                return self::checkAvailability($update, $request);
            } elseif ($action == 'confirmBooking') {
                return self::confirmBooking($update, $request);
            } elseif ($action == 'checkRandomPerson') {
                return self::checkRandomPerson($update, $request);
            } else {
                return parent::error('Sorry! Wrong Action!', $update["responseId"]);
            }
        } else {
            return parent::error('Please check headers!', $update["responseId"]);
        }
    }

    public static function checkAvailability($update, $request) {
        $user_id = $request->header('account');

        $key = self::findOutputContext($update['queryResult']["outputContexts"], "name", "salooncheck-availability-followup");

        $contextBase = explode('contexts/', $update['queryResult']["outputContexts"][$key]['name']);


        $contextBase = $contextBase[0] . "contexts/";
        $date = $update['queryResult']['outputContexts'][$key]['parameters']['date'];
        $time = $update['queryResult']['outputContexts'][$key]['parameters']['time'];

        $servicePerson = $update['queryResult']['outputContexts'][$key]['parameters']['servicePerson'];
        $customerName = $update['queryResult']['outputContexts'][$key]['parameters']['customerName'];

        if ($date == '' || $time == ''):
            return parent::error('Date and Time are required both!', $update["responseId"]);
        endif;
        $date = date("Y-m-d", strtotime($date));
        $timeArray = explode('+', $time);

        $time = $timeArray[0];


        $neededTime = date("g:i A", strtotime($time));
        $time = date("G:i:s", strtotime($time));
        //dd($time);
        if ($servicePerson == '' || $servicePerson == null) {
            $context = 'salooncheck-availability-specificperson-followup';
            return parent::success("Do you have a specific service person in mind?", $update["responseId"], $update['queryResult']["outputContexts"][0]['name'], $contextBase . $context);
        } else {

            //Check only for service person
            $profile = profile::where('name', $servicePerson)->where('user_id', $user_id)->first();

            if ($profile) {

                $checkAvailablity = availability::where('profile_id', $profile->id)->where('start_date', '<=', $date)->where('end_date', '>=', $date)->
                        where('start_time', '<=', $time)
                        ->where('end_time', '>=', $time)
                        ->first();
                if ($checkAvailablity) {


                    if ($customerName != '' || $customerName != null) {
                        $context = 'salooncheck-availability-followup-customername';
                        $contextArray = ['profile_id' => $checkAvailablity->profile_id, 'customerName' => $customerName];
                    } else {
                        $context = 'salooncheck-availability-followup-confirmation';
                        $contextArray = ['profile_id' => $checkAvailablity->profile_id];
                    }


                    $getProfile = profile::where('id', $checkAvailablity->profile_id)->first();
                    return parent::success("" . $getProfile->name . " is available at " . $neededTime . ", go ahead with booking?", $update["responseId"], $update['queryResult']["outputContexts"][0]['name'], $contextBase . $context, $contextArray);
                } else {
                    $context = 'salooncheck-availability-followup-noperson';
                    return parent::success("Sorry " . $servicePerson . " is not available at " . $neededTime . ".", $update["responseId"], $update['queryResult']["outputContexts"][0]['name'], $contextBase . $context);
                }
            } else {
                return parent::error("Sorry Profile " . $servicePerson . " is not found.", $update["responseId"]);
            }
        }

        return parent::success("Success IN", $update["responseId"], $update['queryResult']["outputContexts"][0]['name']);
    }

    public static function findOutputContext($contexts, $field, $value) {
        foreach ($contexts as $key => $contexts) {

            if (strpos($contexts[$field], $value) !== false) {
                return $key;
            }
        }
        return false;
    }

    public static function confirmBooking($update, $request) {

        $key = self::findOutputContext($update['queryResult']["outputContexts"], "name", "salooncheck-availability-followup");
       
        $user_id = $request->header('account');
        $date = $update['queryResult']["outputContexts"][$key]['parameters']['date'];
        $time = $update['queryResult']["outputContexts"][$key]['parameters']['time'];
        $servicePerson = $update['queryResult']["outputContexts"][$key]['parameters']['servicePerson'];
//        $results = array_filter($update['queryResult']["outputContexts"], function($value) {
//            return strpos($value, 'salooncheck-availability-followup-confirmation') !== false;
//        });
        //print_r($update['queryResult']["outputContexts"]);


        
        $profile_id = $update['queryResult']["outputContexts"][$key]['parameters']['profile_id'];
        $customerName = $update['queryResult']["parameters"][$key]['parameters']['customerName'];

      
        if ($date == '' || $time == '' || $servicePerson == '') {
            return parent::error("All the params are required", $update["responseId"]);
        } else {
            $createBooking = booking::create([
                        'date' => $date,
                        'time' => $time,
                        'user_id' => $user_id,
                        'customer_name' => $customerName,
                        'profile_id' => $profile_id
            ]);

            if ($createBooking) {
                return parent::successWithoutContext("Your Booking is Confirmed!", $update["responseId"]);
            } else {
                return parent::error("Sorry, there was some issue in creating a booking for you. Please try again!", $update["responseId"]);
            }
        }
    }

    public static function checkRandomPerson($update, $request) {
        $user_id = $request->header('account');

        $key = self::findOutputContext($update['queryResult']["outputContexts"], "name", "salooncheck-availability-followup");

        $contextBase = explode('contexts/', $update['queryResult']["outputContexts"][$key]['name']);


        $contextBase = $contextBase[0] . "contexts/";
        $date = $update['queryResult']['outputContexts'][$key]['parameters']['date'];
        $time = $update['queryResult']['outputContexts'][$key]['parameters']['time'];

        $servicePerson = $update['queryResult']['outputContexts'][$key]['parameters']['servicePerson'];
        if ($date == '' || $time == ''):
            return parent::error('Date and Time are required both!', $update["responseId"]);
        endif;
        $date = date("Y-m-d", strtotime($date));
        $timeArray = explode('+', $time);
        $time = $timeArray[0];
        $neededTime = date("g:i A", strtotime($time));
        $time = date("G:i:s", strtotime($time));


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

                    $context = 'salooncheck-availability-followup-confirmation';
                    $contextArray = ['profile_id' => $checkAvailablity->profile_id, 'servicePerson' => $getProfile->name];
                    return parent::success("" . $getProfile->name . " is available at " . $neededTime . ", go ahead with booking?", $update["responseId"], $update['queryResult']["outputContexts"][0]['name'], $contextBase . $context, $contextArray);
                } else {
                    $context = 'salooncheck-availability-followup-noperson';

                    $getProfile = profile::where('id', $checkAvailablity->profile_id)->first();
                    return parent::success("" . $getProfile->name . " is not available, would you like any other person to book?", $update["responseId"], $update['queryResult']["outputContexts"][0]['name'], $contextBase . $context);
                }
            }
            return parent::error('No Available Profiles Found', $update["responseId"]);
        else:
            return parent::error('No Profiles found for this account!', $update["responseId"]);
        endif;
    }

}
