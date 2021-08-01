<?php

namespace Modules\Admin\Http\Controllers;

use Illuminate\Http\Request;
use Modules\User\Models\User;
use Carbon\Exceptions\Exception;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
        try {
            $s = User::where('id',(int) $user_id)->first();
            if($s){
                if($s->delete()){
                    return $this->formatAsJson(true,'User has now been banned', $s,'',200);
                }
            }
            return $this->formatAsJson(false,'Cant delete non-existing user', '','',500);

        } catch (Exception $e) {
            return $this->formatAsJson(false,'An error occurred', $e->getMessage(),'',500);
        }
    }

    public function formatAsJson($status, $message='',$data=[],$meta='',$status_code){
        return response()->json([
            'status'=> $status,
            'message'=> $message,
            'data'=> $data,
            'meta'=>$meta
        ],$status_code);
    }

    public function fundUserWallet(Request $request)
    {
        if(!$request->isMethod('PUT')){
           return 'Method not allowed';
        }
        $user = User::where('id',$request->input('user_id'))->first();
        if($user->verified != 'true'){
            return $this->formatAsJson(false,'Cannot fund an unverified user', '','please verify this user first to process',500);
        }
        $user_balance = $user->account_balance;
        $credited = $user->update(['account_balance' => (float)$user_balance + $request->amount]);
        if(!$credited){
            return $this->formatAsJson(false,'Credit failed', '','',500);
        }
        return $this->formatAsJson(true,'Successfully credited user', $user,'',200);
    }

    public function verifyUser(Request $request)
    {
        try {
            $user_id = $request->user_id;
            $user = User::where('id', $user_id)->first();
            $verificationComplete = $user->update(['verified' => 'true']);
            if(!$verificationComplete){
                return $this->formatAsJson(false,'Verification failed', '','',500);
            }
            return $this->formatAsJson(true,'Success, user now verfied', '','',200);
        } catch (Exception $e) {
            return $this->formatAsJson(false,'An error occurred', '', $e->getMessage(),500);
        }
       
    }
}
