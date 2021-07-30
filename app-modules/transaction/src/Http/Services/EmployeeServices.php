<?php

namespace Modules\Transaction\Http\Services;

use Exception;
use App\Model\Employee;
use App\Helpers\AuthTrait;
use Illuminate\Support\Facades\Auth;
use Modules\Transaction\Http\Services\EmployeeServices;

class EmployeeServices
{
    use AuthTrait;
    private $employee;

    public function __construct(Employee $employee)
    {
        $this->employee = $employee;
    }

    public function create($request){
        try {
            $newCreation = Employee::create($request->all());
            $sucess= $this->createdSuccessfullResponse($newCreation);
            $failed= $this->failedtocreateResponse();
            if($newCreation == true){
                return $sucess;
            }
            return $failed;
        } catch (\Exception $e) {
            return $this->errorResponse($e);
        }
    }

    public function showAll(){
        $employees = Employee::with('app_type','department','confirmationProcess','designation','investments_created')
                    ->get();
        $fnArr = [];
        foreach ($employees as $key => $value) {
            $value->fullname = $value->full_name;
            $value->designation_name =  $this->checkIfNotNull($value['designation']) 
                                        ? $value['designation']['name'] : '';

            $value->department_name = $this->checkIfNotNull($value['department']) 
                                       ? $value['department']['department_name'] : '';
            
            // $value->privileges = $this->checkIfNotNull($value['confirmationProcess']) 
            // ? explode(',',$value['confirmationProcess'][$key]['privilege']): [];
            
            // $value->process_description = $this->checkIfNotNull($value['confirmationProcess'])
            //                             ?  $value['confirmationProcess'][$key]['description'] : 'None Assigned';
            
            $value->investments = $this->checkIfNotNull($value['investments_created']) 
            ? $value['investments_created'] : [];

            // unset($value->confirmationProcess);
            // unset($value->department);
            // unset($value->designation);
            array_push($fnArr,$employees);
        }
        return response()->json(['message' => 'List of employees', 'data' => $fnArr[0]]);
    }
    
    public function checkIfNotNull($data)
    {
        return (!empty($data) && !is_null($data));
    }
    public function delete($id){
        $empId = $request->employeeId;
        $employee = Employee::where('id', $empId)->first();
        if ($employee->delete()) {
            return response()->json(['status' => 'true', 'message' => 'Delete Successful']);
        }
        return response()->json(['status' => 'false', 'message' => 'Could not be deleted']);
    }

    public function update($request,$id){
        try {
            $employee = Employee::where('id', $id)->update($request->all());
            if ($employee == true) {
                return response()->json(['status' => true, 'message' => 'Update Successful'], 200);
            } else {
                return response()->json(['status' => false, 'message' => 'Failed to Update'], 401);
            }
        } catch (Exception $e) {
            return $e;
        }
    }

    public function destroyAuthToken(){
        $user= Auth::guard('sanctum')->user();
        try {
            $killedToken=$user->tokens()->delete();
            if($killedToken){
                return response()->json(['status'=> true,'message'=> 'Logout successful'],200);
            }
        } catch (Exception $e) {
            return response()->json(['status'=> false,'message'=> 'Oops! something went wrong please try again'],400);
        }
    }

    public function EmployeeIdByToken()
    {
        $e_id = Auth::check('sanctum') ? Auth::guard('sanctum')->user()->id : false ;
        return $this->EmployeeById($e_id);
    }

    public function EmployeeById($id)
    {
        $employee = Employee::with('app_type','department','confirmationProcess','designation','investments_created')
                                ->where('id', (int)$id)    
                                ->get();
        
        if($this->checkIfNotNull($employee))
            return response()->json(['status'=> true,'data'=>$employee],200);
        return response()->json(['status'=> false,'message'=> 'Not found'],404);
    }


    public static function createEmployeeAppType($request, $employee)
    {
        $e_app = new Employee_Application_Type;
        $e_app->application_type_id = $request->app_type_id;
        $e_app->employee_id = $employee->id;
        $e_app->application_name = $request->application_name;
        $e_app->save();
        return true;
    }

}
