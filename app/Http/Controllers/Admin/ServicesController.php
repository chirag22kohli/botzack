<?php

namespace App\Http\Controllers\Admin;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Service;
use Illuminate\Http\Request;
use Auth;

class ServicesController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\View\View
     */
    public function index(Request $request) {
        $keyword = $request->get('search');
        $perPage = 25;

        if (!empty($keyword)) {
            $services = Service::where('name', 'LIKE', "%$keyword%")
                            ->orWhere('user_id', 'LIKE', "%$keyword%")
                            ->where('user_id', Auth::id())
                            ->latest()->paginate($perPage);
        } else {
            $services = Service::latest()->where('user_id', Auth::id())->paginate($perPage);
        }

        return view('admin.services.index', compact('services'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create() {
        return view('admin.services.create');
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
        $requestData['user_id'] = Auth::id();
        Service::create($requestData);


        $dataArray = [];
        $getAllServices = Service::get();
        foreach ($getAllServices as $service) {
            $dataArray['entries'][]['value'] = $service->name;
        }
        $entityName = "serviceTypes";


        parent::uploadEntityData($dataArray, $entityName);

        return redirect('admin/services')->with('flash_message', 'Service added!');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function show($id) {
        $service = Service::findOrFail($id);

        return view('admin.services.show', compact('service'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     *
     * @return \Illuminate\View\View
     */
    public function edit($id) {
        $service = Service::findOrFail($id);

        return view('admin.services.edit', compact('service'));
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
        $requestData['user_id'] = Auth::id();
        $service = Service::findOrFail($id);
        $service->update($requestData);

        return redirect('admin/services')->with('flash_message', 'Service updated!');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     *
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function destroy($id) {
        Service::destroy($id);

        return redirect('admin/services')->with('flash_message', 'Service deleted!');
    }

}
