<?php

namespace Modules\Transaction\Http\Services;

use Exception;
use App\Model\Employee;
use App\Helpers\AuthTrait;
use Modules\User\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Modules\Transaction\Models\Transaction;
use Modules\Transaction\Http\Services\EmployeeServices;

class AdminServices
{
    private $transaction;

    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    public function showAll()
    {
        $admins = Admin::get();
        return $this->formatAsJson(true,'User has now been banned', $admins,'',200);
    }

    public function create($request){
       
    }

    public function disableUser($request)
    {
        try {
            $s = User::where('id',(int) $user_id)->first();
            if($s){
                if($s->delete()){ //softDelete user
                    return $this->formatAsJson(true,'User has now been banned', $s,'',200);
                }
            }
            return $this->formatAsJson(false,'Cant delete non-existing user', '','',500);

        } catch (Exception $e) {
            return $this->formatAsJson(false,'An error occurred', $e->getMessage(),'',500);
        }
    }

    public function AdminCreditUserWallet($request)
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

    public function AdminVerifyUser($request)
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
    
    public function checkIfNotNull($data)
    {
        return (!empty($data) && !is_null($data));
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
