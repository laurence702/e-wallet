<?php

namespace Modules\Transaction\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Modules\User\Models\User;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Modules\Transaction\Models\Transaction;

class TransactionsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        try {
            $transactions = isset($request->status) ? 
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
    public function store(Request $request) //create new transaction
    {
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

    public function getUserById($user_id) {
        $user = User::where('id', $user_id)->first();
        if($user){
            return $user;
        }
        return false;
       
    }

    public function checkIfSenderBalanceIsSufficient(int $sender_id, float $transaction_amount){
         $userBalance = $this->getUserById($sender_id)->account_balance;
         if($userBalance < $transaction_amount){
             return (bool)0;
         }
         return true;
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
