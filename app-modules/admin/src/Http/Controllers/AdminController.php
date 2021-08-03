<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Modules\User\Models\User;
use Carbon\Exceptions\Exception;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    public function __construct(AdminServices $adminservices){
        $this->adminservices = $adminservices;
        //AdminMiddleware: This is called to make sure all functions here are 
        // performed by only admins
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->adminservices->showAll();
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function banUser($user_id){ //ban user feature
        return $this->AdminServices->disableUser;
    }


    public function fundUserWallet(Request $request)
    {
      return $this->AdminServices->AdminCreditUserWallet($request);
    }

    public function verifyUser(Request $request)
    {
       return $this->AdminServices->AdminVerifyUser($request);
    }
}
