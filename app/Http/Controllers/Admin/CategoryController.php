<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CategoryController extends Controller
{
    //direct addCategory page
    public function addCategory()
    {
        return view('admin.category.addCategory');
    }

    public function createCategory(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name'=>'required',
        ]);
        if($validator->fails()){
            return back()->withErrors($validator)->withInput();
        }

        $data=[
            'category_name'=>request()->name
        ];
        Category::create($data);
        return redirect()->route('admin#category')->with(['categorySuccess'=>"Category Added..."]);
    }

    //direct category page
    public function category(){
        $data=Category::select('categories.*',DB::raw('COUNT(pizzas.category_id) as count'))
              ->leftJoin('pizzas','pizzas.category_id','categories.category_id')
              ->groupBy('categories.category_id')
              ->paginate(4);
        return view('admin.category.list')->with(['category'=>$data]);
    }

    //delete category page
    public function deleteCategory($id)
    {
        Category::where('category_id',$id)->delete();
        return back()->with(['deleteSuccess'=>'Category Deleted...']);
    }

    //edit category page
    public function editCategory($id)
    {
        $data=Category::where('category_id',$id)->first();
        return view('admin.category.update')->with(['category'=>$data]);
    }

    //update category
    public function updateCategory(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'name'=>'required',
        ]);
        if($validator->fails()){
            return back()->withErrors($validator)->withInput();
        }
        $updateData=[
            'category_name'=>$request->name
        ];
        Category::where('category_id',$request->id)->update($updateData);
        return redirect()->route('admin#category')->with(['updateSuccess'=>'Category Updated...']);
    }

    //search category page
    public function searchCategory(Request $request)
    {
        $data = Category::where('category_name','like','%'.$request->searchData.'%')->paginate(4);
        $data->appends($request->all());
        return view('admin.category.list')->with(['category'=>$data]);
    }

}
