<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\ProfileService;
use Illuminate\Http\Request;
use Auth;
class ProfileServicesController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request) {
        $keyword = $request->get('search');
        $perPage = 25;

        if (!empty($keyword)) {
            $profileservices = ProfileService::where('profile_id', 'LIKE', "%$keyword%")
                            ->orWhere('service_id', 'LIKE', "%$keyword%")
                            ->orWhere('user_id', 'LIKE', "%$keyword%")
                            ->latest()->paginate($perPage);
        } else {
            $profileservices = ProfileService::latest()->paginate($perPage);
        }

        return view('admin.profile-services.index', compact('profileservices'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create(Request $request) {
        $getAllServices = \App\Service::get();
        $getProfileServices = ProfileService::where('profile_id',$request->get('profile'))->with('serviceName')->get();
      //  dd($getProfileServices);
        return view('admin.profile-services.create', ['services' => $getAllServices,'profileServices'=>$getProfileServices]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request) {

        $requestData = $request->all();

        foreach ($request->input('service_id') as $service) {
            $finalData['service_id'] = $service;
            $finalData['user_id'] = Auth::id();
             $finalData['profile_id'] = $requestData['profile_id'];
            ProfileService::firstOrCreate($finalData);
        }


        return redirect('admin/profiles')->with('flash_message', 'Profile Services added!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function show($id) {
        $profileservice = ProfileService::findOrFail($id);

        return view('admin.profile-services.show', compact('profileservice'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function edit($id) {
        $profileservice = ProfileService::findOrFail($id);

        return view('admin.profile-services.edit', compact('profileservice'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id) {

        $requestData = $request->all();

        $profileservice = ProfileService::findOrFail($id);
        $profileservice->update($requestData);

        return redirect('admin/profile-services')->with('flash_message', 'ProfileService updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id) {
        ProfileService::destroy($id);

        return redirect('admin/profile-services')->with('flash_message', 'ProfileService deleted!');
    }

}
