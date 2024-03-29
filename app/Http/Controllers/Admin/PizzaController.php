<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Pizza;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\File;

class PizzaController extends Controller
{
    //direct pizza page
    public function pizza(){
        $data=Pizza::paginate(5);
        if(count($data)==0){
            $emptyStatus=0;
        }else{
            $emptyStatus=1;
        }
        return view('admin.pizza.list')->with(['pizza'=>$data,'status'=>$emptyStatus]);
    }
    //direct create pizza page
    public function createPizza(){
        $category=Category::get();
        return view('admin.pizza.create')->with(['category'=>$category]);
    }
    //insert pizza
    public function insertPizza(Request $request){
        $validator = Validator::make($request->all(),[
            'name'=>'required',
            'image'=>'required',
            'price'=>'required',
            'publish'=>'required',
            'category'=>'required',
            'discount'=>'required',
            'buyOneGetOne'=>'required',
            'waitingTime'=>'required',
            'description'=>'required',
        ]);
        if($validator->fails()){
            return back()->withErrors($validator)->withInput();
        }
        $file=$request->file('image');
        $fileName=uniqid().'_'.$file->getClientOriginalName();
        $file->move(public_path().'/uploads/',$fileName);
        // $file->move(public_path('uploads'),$fileName);

        $data=$this->requestPizzaData($request,$fileName);
        Pizza::create($data);
        return redirect()->route('admin#pizza')->with(['createSuccess'=>"Pizza Create Successfully!"]);
    }

    //delete pizza
    public function deletePizza($id){
        $data=Pizza::select('image')->where('pizza_id',$id)->first();
        $fileName=$data['image'];
        Pizza::where('pizza_id',$id)->delete();//delete in database

        //delete in pj
        if(File::exists(public_path().'/uploads/'.$fileName)){
            File::delete(public_path().'/uploads/'.$fileName);
        }
        return back()->with(['deleteSuccess'=>"Pizza Delete Successfully!"]);
    }

    //pizza info
    public function pizzaInfo($id){
        $data=Pizza::where('pizza_id',$id)->first();
        return view('admin.pizza.info')->with(['pizza'=>$data]);
    }

    //edit pizza page
    public function editPizza($id){
        $category=Category::get();
        $data=Pizza::select('pizzas.*','categories.category_id','categories.category_name')
            ->join('categories','pizzas.category_id','categories.category_id')
            ->where('pizza_id',$id)
            ->first();

        return view('admin.pizza.edit')->with(['pizza'=>$data,'category'=>$category]);
    }

    //update pizza page
    public function updatePizza($id,Request $request){
        $validator = Validator::make($request->all(),[
            'name'=>'required',
            'price'=>'required',
            'publish'=>'required',
            'category'=>'required',
            'discount'=>'required',
            'buyOneGetOne'=>'required',
            'waitingTime'=>'required',
            'description'=>'required',
        ]);
        if($validator->fails()){
            return back()->withErrors($validator)->withInput();
        }
        $updateData=$this->requestUpdatePizzaData($request);
        if(isset($updateData['image'])){
            //get old image name
            $data=Pizza::select('image')->where('pizza_id',$id)->first();
            $fileName=$data['image'];
            //delete old image
            if(File::exists(public_path().'/uploads/'.$fileName)){
                File::delete(public_path().'/uploads/'.$fileName);
            }
            //get new image data
            $file=$request->file('image');
            $fileName=uniqid().'_'.$file->getClientOriginalName();
            $file->move(public_path().'/uploads/',$fileName);
            $updateData['image']=$fileName;

        }
            Pizza::where('pizza_id',$id)->update($updateData);
            return redirect()->route('admin#pizza')->with(['updateSuccess'=>'Pizza Updated!']);
    }

    //search pizza
    public function searchPizza(Request $request){
        $searchKey=$request->table_search;
        $searchData=Pizza::orWhere('pizza_name','like','%'.$searchKey.'%')
                          ->orWhere('price','like','%'.$searchKey.'%')
                          ->paginate(4);

        $searchData->appends($request->all());
        if(count($searchData)==0){
            $emptyStatus=0;
        }else{
            $emptyStatus=1;
        }
        return view('admin.pizza.list')->with(['pizza'=>$searchData,'status'=>$emptyStatus]);
    }

    //look category item
    public function categoryItem($id){
        $data=Pizza::select('pizzas.*','categories.category_name as categoryName')
                     ->join('categories','categories.category_id','pizzas.category_id')
                     ->where('pizzas.category_id',$id)
                     ->paginate(4);
        return view('admin.category.item')->with(['pizza'=>$data]);
    }

    private function requestUpdatePizzaData($request){
        $arr=[
            'pizza_name'=>$request->name,
            'price'=>$request->price,
            'publish_status'=>$request->publish,
            'category_id'=>$request->category,
            'discount_price'=>$request->discount,
            'buy_one_get_one_status'=>$request->buyOneGetOne,
            'waiting_time'=>$request->waitingTime,
            'description'=>$request->description,
        ];
        if(isset($request->image)){
            $arr['image']=$request->image;
        }
        return $arr;
    }

    //request pizza data
    private function requestPizzaData($request,$fileName){
        return[
            'pizza_name'=>$request->name,
            'image'=>$fileName,
            'price'=>$request->price,
            'publish_status'=>$request->publish,
            'category_id'=>$request->category,
            'discount_price'=>$request->discount,
            'buy_one_get_one_status'=>$request->buyOneGetOne,
            'waiting_time'=>$request->waitingTime,
            'description'=>$request->description,
        ];
    }
}
