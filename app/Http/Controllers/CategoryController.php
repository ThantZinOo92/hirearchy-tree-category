<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Language;
use Illuminate\Http\Request;
use Validator;

class CategoryController extends Controller
{


    protected $category;
    protected $language;

    /**
     * CategoryController constructor.
     *
     * @param  Category  $category
     * @param  Language  $language
     */
    public function __construct(Category $category,Language $language)
    {
        $this->category = $category;
        $this->language = $language;
    }

    public function index()
    {
        $categories = $this->category->all();
        return response()->json($categories);
    }

    public function getMainCategories()
    {
        $main_categories = $this->category->where('parent_id','=',0)->get();
        return response()->json($main_categories);
    }

    public function getCategoriesByParentID($parent_id)
    {
        if($parent_id > 0){
            $categories = $this->category->where('parent_id','=',$parent_id)->get();
            if($categories){
                return response()->json($categories);
            }
            else {
                return response()->json(["message" => "Category Not Found."], 404);
            }
        }
        else
        {
            return response()->json(["message" => "Category Not Found."], 404);
        }


    }

    public function store(Request $request)
    {
        $rules=array(
            'name'  =>"required|min:2|max:30",
        );
        $validator=Validator::make($request->all(),$rules);
        if($validator->fails())
        {
            return response()->json($validator->errors(), 400);
        }
        else
        {
            $category = $this->category;
            $category->name = $request->name;
            if($request->has('is_main_category') || !$request->has('parent_id')){
                $category->parent_id = 0;
            }
            else{
                $category->parent_id = $request->parent_id;
            }
            $result = $category->save();

            if($result){
                if($request->has('language_name') && $request->has('translation')){
                    foreach ($request->language_name as $key=>$value) {
                        $language = new Language;
                        $language->name = $request->language_name[$key];
                        $language->translation = $request->translation[$key];
                        $language->category_id = $category->id;
                        $language->save();
                    }
                }

                return response()->json([
                    "message" => "Category Successfully Added."
                ], 201);
            }

            else{
                return response()->json([
                    "message" => "Operation failed"
                ], 422);
            }
      }
    }

    public function create($id = 0){
        if($id > 0){
            $category = $this->category->find($id);
            $type = 'update';
            return view('create')->with('category',$category)->with('type',$type);
        }
        else{
            $type = 'create';
            return view('create')->with('type',$type);
        }


    }

    public function update(Request $request, $id)
    {
        $rules=array(
            'name'  =>"required|min:2|max:30",
        );
        $validator=Validator::make($request->all(),$rules);
        if($validator->fails())
        {
            return response()->json($validator->errors(), 400);
        }
        else
        {
            if ($this->category->where('id', $id)->exists()) {
                $category = $this->category->find($id);
                $category->name = $request->name;
                if($category->parent_id > 0){
                    $category->parent_id = $request->parent_id;
                }
                $category->languages()->delete();
                $result = $category->save();
                if($result){
                    if($request->has('language_name') && $request->has('translation')){
                        foreach ($request->language_name as $key=>$value) {
                            $language = new Language;
                            $language->name = $request->language_name[$key];
                            $language->translation = $request->translation[$key];
                            $language->category_id = $category->id;
                            $language->save();
                        }
                    }
    
                    return response()->json([
                        "message" => "Category Successfully Updated."
                    ], 201);
                }
                else
                {
                    return response()->json([
                        "message" => "Operation failed"
                    ], 422);
                }
            }
            else
            {
                return response()->json([
                    "message" => "Category Not Found."
                ], 404);
            }
        }
    }


}
