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
            $action = $update['queryResult']['action'];

            if ($action == 'checkAvailability') {
                return self::checkAvailability($update, $request);
            } elseif ($action == 'confirmBooking') {
                return self::confirmBooking($update, $request);
            } else {
                return parent::error('Sorry! Wrong Action!', $update["responseId"]);
            }
        } else {
            return parent::error('Please check headers!', $update["responseId"]);
        }
    }

    public static function checkAvailability($update, $request) {
        $user_id = $request->header('account');
        $contextBase = explode('context/', $update['queryResult']["outputContexts"][0]['name']);
        $contextBase = $contextBase[0] . "context/";
        $date = $update['queryResult']['parameters']['date'];
        $time = $update['queryResult']['parameters']['time'];
        $servicePerson = $update['queryResult']['parameters']['servicePerson'];
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

                        return parent::success("" . $getProfile->name . " is available at " . $neededTime . ", go ahead with booking?", $update["responseId"], $update['queryResult']["outputContexts"][0]['name'], $contextBase . $context);
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
        } else {

            //Check only for service person
            $profile = profile::where('name', $servicePerson)->where('user_id', $user_id)->first();

            if ($profile) {

                $checkAvailablity = availability::where('profile_id', $profile->id)->where('start_date', '<=', $date)->where('end_date', '>=', $date)->
                        where('start_time', '<=', $time)
                        ->where('end_time', '>=', $time)
                        ->first();
                if ($checkAvailablity) {
                    $context = 'salooncheck-availability-followup-confirmation';
                    $getProfile = profile::where('id', $checkAvailablity->profile_id)->first();
                    return parent::success("" . $getProfile->name . " is available at " . $neededTime . ", go ahead with booking?", $update["responseId"], $update['queryResult']["outputContexts"][0]['name'], $contextBase . $context);
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

    public static function confirmBooking($update, $request) {
        $user_id = $request->header('account');
        $date = $update['queryResult']["outputContexts"][0]['parameters']['date'];
        $time = $update['queryResult']["outputContexts"][0]['parameters']['time'];
        $servicePerson = $update['queryResult']["outputContexts"][0]['parameters']['servicePerson'];
        $customerName = $update['queryResult']["parameters"]['customerName'];
        if ($date == '' || $time == '' || $servicePerson == '') {
            return parent::error("All the params are required", $update["responseId"]);
        } else {
            $createBooking = booking::create([
                        'date' => $date,
                        'time' => $time,
                        'user_id' => $user_id,
                        'customer_name' => $customerName
            ]);

            if ($createBooking) {
                return parent::successWithoutContext("Your Booking is Confirmed!", $update["responseId"]);
            } else {
                return parent::error("Sorry, there was some issue in creating a booking for you. Please try again!", $update["responseId"]);
            }
        }
    }

}
