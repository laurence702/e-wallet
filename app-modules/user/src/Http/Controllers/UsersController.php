<?php

namespace Modules\User\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Modules\User\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Validation\Rule;

class UsersController extends Controller
{

    // public $employeeServices;
    // public function __construct(EmployeeServices $employeeServices){
    //     $this->EmployeeServices = $employeeServices;
    // }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            if($request->has('verified')){
                $users = User::where('verified',$request->verified)->get()->load(['money_received','money_sent']);
            }
                $users = User::withTrashed()->get()->load(['money_received','money_sent']);
            if(!$users){ 
                return $this->formatAsJson(false,'No users created','','',404); 
            }
            return $this->formatAsJson(true,'List of all users',$users,'',200);
        } catch (Exception $e) {
            return $this->formatAsJson(true,'An error occurred', $e->getMessage(),'',500);
        }
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
      
        $data = $request->except(['account_balance','pin']);
        $rules = [
            'last_name' => 'required|min:3',
            'first_name' => 'required|min:3',
            'email' => 'required|unique:users',
            'phone' => 'required|unique:users|min:8',
        ];
        $validator = \Validator::make($data, $rules);

        if ($validator->fails()) {
            return $this->formatAsJson(false,'Validation didnt pass',[],$validator->errors(),422);
        }
        try {
            $newUser = User::create($request->all());
            if($newUser){
                return $this->formatAsJson(true,'User was created successfully',
                User::latest()->first(),
                'your secret pin for transfers is: '.User::latest()->first()->pin,201);
            }
        } catch (Exception $e) {
            return $this->formatAsJson(true,'User failed to create', [],$e->getMessage(),500);
        }

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        try {
            if($request->has('balance')){
                $users = User::find((int) $id)->account_balance;              
            }else{
                $_users = User::find((int) $id);
                $users = $_users ? $_users->load(['money_received','money_sent']): null;
            }
            if(!$users || $users == null){ return $this->formatAsJson(true,'User not found','','',404); }
            return $this->formatAsJson(true,'User info',$users,'',200);
        } catch (Exception $e) {
            return $this->formatAsJson(true,'An error occurred', $e->getMessage(),'',500);
        }
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
    public function destroy($id) //ban user
    {
       return $lastId = User::latest()->first()->id;
    }

    public function formatAsJson($status, $message='',$data=[],$meta='',$status_code){
        return response()->json([
            'status'=> $status,
            'message'=> $message,
            'data'=> $data,
            'meta'=>$meta
        ],$status_code);
    }
    
}
