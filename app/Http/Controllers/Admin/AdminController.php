<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    //admin profile direct page
    public function profile(){
        $id = optional(auth()->user())->id;
        $userData = User::where('id',$id)->first();
        return view('admin.profile.index')->with(['user'=>$userData]);
    }

    //update profile page
    public function updateProfile($id,Request $request){
        $validator = Validator::make($request->all(),[
            'name'=>'required',
            'email'=>'required',
            'phone'=>'required',
            'address'=>'required',
        ]);
        if($validator->fails()){
            return back()->withErrors($validator)->withInput();
        }
        $updateData=$this->requestUserData($request);
        User::where('id',$id)->update($updateData);
        return back()->with(['updateSuccess'=>'User Information Updated!']);
    }

    //change password
    public function changePassword($id,Request $request){
        $validator = Validator::make($request->all(),[
            'oldPassword'=>'required',
            'newPassword'=>'required',
            'confirmPassword'=>'required',
        ]);
        if($validator->fails()){
            return back()->withErrors($validator)->withInput();
        }

        $data=User::where('id',$id)->first();

        $oldPassword=$request->oldPassword;
        $newPassword=$request->newPassword;
        $confirmPassword=$request->confirmPassword;
        $hashedPassword=$data['password'];

        if (Hash::check($oldPassword, $hashedPassword)) {
            if($newPassword != $confirmPassword){
                return back()->with(['notSameError'=>'Oops...New Password and Confirm Password Are Not Match! Try Again...']);
            }else{
                if(strlen($newPassword) <=6 || strlen($confirmPassword) <=6){
                    return back()->with(['lengthError'=>'Password Must Be More Than 6']);
                }else{

                    $hash=Hash::make($newPassword);
                    User::where('id',$id)->update([
                        'password'=>$hash
                    ]);
                    return back()->with(['success'=>'Password Change Successfully...']);
                }
            }
        }else{
            return back()->with(['notMatchError'=>'Password Do Not Match! Try Again...']);
        }
    }

    //change psw page
    public function changePasswordPage(){
        return view('admin.profile.changePassword');
    }


    private function requestUserData($request){
        return [
            'name'=>$request->name,
            'email'=>$request->email,
            'phone'=>$request->phone,
            'address'=>$request->address
        ];

    }
}
