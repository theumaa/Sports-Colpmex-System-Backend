<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Guest;
use App\Models\GuestPayment;
use App\Models\Income;
use App\Models\Package;
use App\Models\ResourceTracking;
use App\Models\Sports;
use App\Models\UserTracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Resources\CommonResource;
use App\Models\User;
use DB;
use Auth;
class CommonController extends Controller
{
    public function validateLogin(Request $request)
    {
       return Validator::make($request->all(), [
        'email' => 'required|email',
        'password' => 'required|max:18|min:5',
        'remember'=>'boolean',
        ]);
    }

    public function validateRegistration(Request $request)
    {
       return Validator::make($request->all(), [
        'email' => 'required|email',
        'password' => 'required|max:18|min:5|confirmed|unique:users',
        'name'=>'required|max:40',
        'package_id'=>'required|exists:packages,id',
        'birthday'=>'required',
        'contact' => 'required|numeric|digits_between:10,15',
        ]);
    }

    public function logout(Request $request)
    {
      Auth::guard('web')->logout();
      $request->session()->regenerateToken();
      return $request->session()->invalidate();
    } 


    public function error(){
      return  response('login as admin');
    }
    public function testAdmin(){
      return  response('you are admin');
    }
    public function login(Request $request)
    {
        $validator=$this->validatelogin($request);
        if ($validator->fails()) {
         return response()->json(['error'=>$validator->errors()]);
        }
        $remember=$request['remember'];
          unset($request['remember']);
        if (auth()->attempt($request->all(),$remember)) {
            
            return new CommonResource(auth()->user());
        } else {
            // Authentication failed
            return "Invalid Entry";
        }
    }

    public function register(Request $request)
    {
        try{
            DB::beginTransaction();
            $validator=$this->validateRegistration($request);
            if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()]);
            }
            $user= User::create($request->all());
            $data['user_id']=$user->id;
            $package=Package::find($user->package_id);
            $days_left=$package->days*4;
            $data['hours_left']=$package->hours* $days_left;
            UserTracking::create($data);
            $user= new CommonResource($user);
            
            DB::commit();
            return $user;
        }
        catch(\Exception $e){
            DB::rollBack();
        } 
    }

    
    public function getAvailability(Request $request)
    {
      $validator=$this->validateAvailabilityRequest($request);
      if ($validator->fails()) {
        return response()->json(['error'=>$validator->errors()]);
       }
       
      return new CommonResource(ResourceTracking::where('date',$request->date)->whereRelation('sports','name',$request->sport)->get());
    }

    public function setAvailability(Request $request)
    {
        $validator=$this->validateAvailabilitySetRequest($request);
        if ($validator->fails()) {
          return response()->json(['error'=>$validator->errors()]);
         }
         try
         {
            DB::beginTransaction();
            $guest= Guest::create($request->except(['date','support','from','to','court','sport']));
            $request['sport_id']=Sports::where('name',$request->sport)->first()->id;
            $request['trackable_type']=get_class($guest);
            $request['trackable_id']=$guest->id;
            $result= ResourceTracking::create($request->except(['sport','teen','name','nic','contact']));
            DB::commit();
            return $result;
         }  
         catch(\Exception $e)
         {
            DB::rollBack();
         }  
    
    }


    public function validateAvailabilityRequest(Request $request)
    {
       return Validator::make($request->all(), [
        'date' => 'required|date_format:Y-m-d|date',
        'sport' => 'required|exists:sports,name',

        ]);
    }
    public function validateAvailabilitySetRequest(Request $request)
    {
       return Validator::make($request->all(), [
        'date' => 'required|date_format:Y-m-d|date',
        'sport' => 'required|exists:sports,name',
        'from'=>'required|date_format:H:i:s',
        'court'=>'required|integer|min:0|max:3',
        'teen' => 'required|boolean',
        'name' => 'required|string',
        'nic'=>'required|string|min:10|max:15',
        'contact' => 'required|numeric|digits_between:10,15',
        ]);
    }

    public function validateUserBooking(Request $request)
    {
        return Validator::make($request->all(), [
            'date' => 'required|date_format:Y-m-d|date',
            'sport' => 'required|exists:sports,name',
            'from'=>'required|date_format:H:i:s',
            'court'=>'required|integer|min:0|max:3',
            ]); 
    }

    public function userBooking(Request $request)
    {
        $validator=$this->validateUserBooking($request);
        if ($validator->fails()) {
          return response()->json(['error'=>$validator->errors()]);
         }
         try{
            DB::beginTransaction();
            $request['sport_id']=Sports::where('name',$request->sport)->first()->id;
            $request['trackable_id']=auth()->user()->id;
            $request['trackable_type']=get_class(auth()->user());
            ResourceTracking::create($request->except(['sport']));
            $usertrack=UserTracking::find(auth()->user()->id);
            if($usertrack->hours_left>0)
            {
             $usertrack->decrement('hours_left',1);
            }
            else
            {
              return 'error';  
            }
            DB::commit();
            return 'success';
         }catch(\Exception $e){
            DB::rollBack();
         }
    }

    public function updateStatus(Request $request){
     $resourceTracking=ResourceTracking::find($request['id']);
     if($request['type']=='paid')
     {
      $resourceTracking->update(['status'=>'paid']);
      if($resourceTracking->trackable_type!=get_class(auth()->user()))
      {
       GuestPayment::create(['amount'=>$request['amount'],'resource_tracking_id'=>$request['id']]);
      }
      
     }
     else{
      $resourceTracking->update(['status'=>$request['type']]);

     }
    }

    public function adminDashboard()
    { $data=[];
      $data['users'] =User::count()-1;
      $data['guests']=Guest::distinct()->count('contact');
      $data['completed']=ResourceTracking::where('status','completed')->count();
      $data['ongoing']=ResourceTracking::where('status','ongoing')->count();
      $paid=ResourceTracking::where('status','paid')->where('trackable_type',get_class(auth()->user()))->get();
      $price=0;
      foreach($paid as $user)
      {
       $price+=$user->package->price;
      }
      $guestPaid=GuestPayment::get();
      foreach($guestPaid as $payment)
      {
        $price+=$payment->amount;
      }
      $data['income']=$price;
      return $data;
    }

    public function users()
    {
      $users=User::all();
      foreach($users as $user)
      {
       $user['package']=Package::find($user['package_id'])->value('name');
       unset($user['package_id']);
      }
      return new CommonResource($users);
    }
    public function completedSessions(){
      $tracking=ResourceTracking::where('status','paid')->get();
      foreach($tracking as $track)
      {
        $track['sport']=Sports::find($track['sport_id'])->name;
        unset($track['sport_id']);
      }
      return new CommonResource($tracking); 
    }
    public function guestMembers(){
      $guests=Guest::all();
      foreach($guests as $guest)
      {
       $guest['Adult/Teen']=$guest['teen']?'Teen':'Adult'; 
      }
      return new CommonResource($guests);
    }
    public function upcomingSessions(){
      $tracking=ResourceTracking::where('status','ongoing')->get();
      foreach($tracking as $track)
      {
        $track['sport']=Sports::find($track['sport_id'])->name;
        unset($track['sport_id']);
      }
      return new CommonResource($tracking);    
    }

}
