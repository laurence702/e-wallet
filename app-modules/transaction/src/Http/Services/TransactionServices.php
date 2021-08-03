<?php

namespace Modules\Transaction\Http\Services;

use Exception;
use App\Model\Employee;
use App\Helpers\AuthTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Modules\Transaction\Models\Transaction;
use Modules\Transaction\Http\Services\EmployeeServices;

class TransactionServices
{
    private $transaction;

    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    public function create($request){
       
    }

    public function getAll($request){
        try {
            $transactions = ($request->has('status')) ? 
                Transaction::where('status',$request->status)->get()->load(['sender','recipient']) : 
                Transaction::get()->load(['sender','recipient']);
            if(count($transactions) == 0){ 
                return $this->formatAsJson(true,'No transaction created','','',404); 
            }
            return $this->formatAsJson(true,'List of all transactions',$transactions,'',200);
        } catch (Exception $e) {
            return $this->formatAsJson(true,'An error occurred', $e->getMessage(),'',500);
        }
    }

    public function initiateTransaction($request) 
    {
         //ensure user cant transfer to himself to avoid fraud
         if($request->sender_id === $request->receiver_id){
            return $this->formatAsJson(false,'Cant transfer to self, you will be blocked after 5 attempts', [],'please contact support',402);
        }
        $sender = $this->getUserById($request->sender_id);
        if($sender->verified !== 'true'){
            return $this->formatAsJson(false,'Unverfied Users cant make transfers', [],'please contact support',402);
        }
        if (!Hash::check($request->input('pin'), $sender->pin_hash)) {
            return $this->formatAsJson(false,'wrong pin,please check email for pin or contact support for help',[],'',401);
        }
       
       try {
           if(!$this->checkIfSenderBalanceIsSufficient($request->sender_id,$request->transaction_amount)){
                return $this->formatAsJson(false,'Balance is insufficient', [],'',402);
           }
            DB::beginTransaction();
            $sent = $this->debitSender($request->sender_id, $request->transaction_amount); //debit the sender
            $received = $this->creditReceiver($request->receiver_id, $request->transaction_amount); //then credit the receiver
            if($sent && $received){
                $newTransaction = Transaction::create($request->all()); //then save it to transaction history
                DB::commit();
                if($newTransaction){
                    return $this->formatAsJson(true,'Successful',Transaction::latest()->first(),'',200);
                }
            }
            return $this->formatAsJson(false,'Failed',[],'',500);
        } catch (Exception $e) {
            return $this->formatAsJson(false,'Transaction failed', [],$e->getMessage(),500);
        }
    }

    public function creditReceiver(int $receiver_id,  float $transaction_amount){
        
        $receiverBalance = $this->getUserById($receiver_id)->account_balance;
        $newBalance =  $receiverBalance +  $transaction_amount;
        $query = User::where('id',$receiver_id)->update(['account_balance' => $newBalance]);
        if($query){
            DB::commit();
            return true;
        }
    }
    public function debitSender(int $sender_id,  float $transaction_amount){
        $senderBalance = $this->getUserById($sender_id)->account_balance;
        $newBalance = $senderBalance -  $transaction_amount;
        $query = User::where('id',$sender_id)->update(['account_balance' => $newBalance]);
        if($query){
            DB::commit();
            return true;
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

    public function getUserById($user_id) {
        $user = User::where('id', $user_id)->first();
        if($user){return $user;}else{ return false; }
    }

    public function checkIfSenderBalanceIsSufficient(int $sender_id, float $transaction_amount){
        $userBalance = $this->getUserById($sender_id)->account_balance;
        if($userBalance < $transaction_amount){
            return (bool)0;
        }
        return true;
   }

}
