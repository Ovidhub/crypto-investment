<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Plans;
use App\Models\Signal;
use App\Models\User_plans;
use App\Models\User_signal;
class InvPlanController extends Controller
{
     //Add plan requests
     public function addplan(Request $request){
       
        $plan=new Plans();
        $plan->name= $request['name'];
        $plan->price= $request['price'];
        $plan->min_price= $request['min_price'];
        $plan->max_price= $request['max_price'];
        $plan->minr=$request['minr'];
        $plan->maxr=$request['maxr'];
        $plan->gift=$request['gift'];
        $plan->expected_return= $request['return'];
        $plan->increment_type= $request['t_type'];
        $plan->increment_interval= $request['t_interval'];
        $plan->increment_amount= $request['t_amount'];
        $plan->expiration= $request['expiration'];
        $plan->type= 'Main';
        
        // Handle stock investment fields
        $plan->plan_type = $request['plan_type'] ?? 'investment';
        if ($plan->plan_type === 'stock') {
            $plan->stock_symbol = $request['stock_symbol'];
            $plan->stock_company = $request['stock_company'];
            $plan->stock_description = $request['stock_description'];
        }
        
        $plan->save();
        return redirect()->back()->with('success', 'Plan created Sucessfully!');
    }


    //Update plan
    public function updateplan(Request $request){
        $updateData = [
            'name' => $request['name'],
            'price' => $request['price'],
            'min_price' => $request['min_price'],
            'max_price' => $request['max_price'],
            'minr' => $request['minr'],
            'maxr' => $request['maxr'],
            'gift' => $request['gift'],
            'expected_return' => $request['return'],
            'increment_type' => $request['t_type'],
            'increment_amount' => $request['t_amount'],
            'increment_interval' => $request['t_interval'],
            'type' => 'Main',
            'expiration' => $request['expiration'],
            'plan_type' => $request['plan_type'] ?? 'investment',
        ];

        // Add stock fields if plan type is stock
        if ($request['plan_type'] === 'stock') {
            $updateData['stock_symbol'] = $request['stock_symbol'];
            $updateData['stock_company'] = $request['stock_company'];
            $updateData['stock_description'] = $request['stock_description'];
        } else {
            // Clear stock fields if changing from stock to investment
            $updateData['stock_symbol'] = null;
            $updateData['stock_company'] = null;
            $updateData['stock_description'] = null;
        }

        Plans::where('id', $request['id'])->update($updateData);
        return redirect()->back()->with('success', 'Plan Successfully Updated');
    }

    //Trash Plans route
    public function trashplan($id){
        
        // Delete this plan from every user account that have bought this plan
        $usersplan = User_plans::where('plan', $id)->get();
        if (count($usersplan) > 0) {
            foreach($usersplan as $plns){
                User_plans::where('id', $plns->id)->delete(); 
            }
        }

        //remove users from the plan before deleting
        $users=User::where('plan',$id)->get();
        foreach($users as $user){
            User::where('id',$user->id)
            ->update([
                'plan' => 0,
                //'confirmed_plan' => 0,
            ]);  
        }
        Plans::where('id',$id)->delete();
        return redirect()->back()
        ->with('success', 'Investment Plan deleted Successfully!');
    }
    
    
     //Add signal requests
    public function addsignal(Request $request){
       
        $signal=new Signal();
        $signal->name= $request['name'];
        $signal->price= $request['price'];
        $signal->increment_amount= $request['increment_amount'];
        $signal->type= 'Main';
        $signal->save();
        return redirect()->back()->with('success', 'Signal created Sucessfully!');
    }



     //Update plan
     public function updatesignal(Request $request){
       
        Signal::where('id', $request['id'])
        ->update([
            'name' => $request['name'],
            'price' => $request['price'],
            'increment_amount'=> $request['increment_amount'],
            'type' => 'Main',
           
        ]);
        return redirect()->back()->with('success', ' Successfully Updated');
    }

     

    //Trash Plans route
    public function trashsignal($id){
        
        // Delete this plan from every user account that have bought this plan
        $usersignal = User_signal::where('signals', $id)->get();
        if (count($usersignal) > 0) {
            foreach($usersignal as $slns){
                User_signal::where('id', $slns->id)->delete(); 
            }
        }

        //remove users from the plan before deleting
        $users=User::where('signals',$id)->get();
        foreach($users as $user){
            User::where('id',$user->id)
            ->update([
                'signals' => 0,
                //'confirmed_plan' => 0,
            ]);  
        }
        Signal::where('id',$id)->delete();
        return redirect()->back()
        ->with('success', 'Signals deleted Successfully!');
    }


     
   
}
