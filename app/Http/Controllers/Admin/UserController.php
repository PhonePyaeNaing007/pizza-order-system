<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    //direct user list page
    public function userList(){
        $userData=User::where('role','user')->paginate(4);
        return view('admin.user.userList')->with(['user'=>$userData]);
    }

    //direct admin list page
    public function adminList(){
        $userData=User::where('role','admin')->paginate(4);
        return view('admin.user.adminList')->with(['admin'=>$userData]);
    }

    //user acc search
    public function userSearch(Request $request){
        $response=$this->search('user',$request);
        return view('admin.user.userList')->with(['user'=>$response]);
    }

    //admin acc search
    public function adminSearch(Request $request){
        $response=$this->search('admin',$request);
        return view('admin.user.adminList')->with(['admin'=>$response]);
    }

    //delete acc
    public function userDelete($id){
        User::where('id',$id)->delete();
        return back()->with(['deleteSuccess'=>'Delete Successfully!']);
    }

    //data searching
    private function search($role,$request){
        $searchData=User::where('role',$role)
                        ->where(function($query) use($request){
                            $query->orWhere('name','like','%'.$request->searchData.'%')
                            ->orWhere('email','like','%'.$request->searchData.'%')
                            ->orWhere('phone','like','%'.$request->searchData.'%')
                            ->orWhere('address','like','%'.$request->searchData.'%');
                        })
                     ->paginate(5);
        $searchData->appends($request->all());
        return $searchData;
    }
}
