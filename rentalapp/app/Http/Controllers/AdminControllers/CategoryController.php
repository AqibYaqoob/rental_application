<?php

namespace App\Http\Controllers\AdminControllers;

use App\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Mockery\Exception;

class CategoryController extends Controller
{
    public function index()
    {
        return view('company_portal.category.add_category');
    }
    public function addCategory(Request $request)
    {
        $this->validate($request, [
            'category' => 'required|string|min:3|max:60',
            'description' => 'required|string|max:150'
        ],
        [
            'category.required' => 'Category name is required',
            'category.string' => 'Category name must be a string',
            'category.min' => 'Category name must have at least 5 characters',
            'category.max' => 'Category name should not exceed 60 characters',
            'description.required' => 'Category description is required',
            'description.string' => 'Category description must be a string',
            'description.max' => 'Category description should not exceed 150 characters'
        ]);
        $category = new Category();
        $category->name = $request->category;
        $category->description = $request->description;
        $category->tenant_id = Auth::user()->TenantId;
        $category->created_at = Carbon::now();
        $category->save();
        return redirect()->route('category.list')->with('success', 'Category has been added');
    }
    public function listCategories()
    {
        $categories = Category::where('tenant_id', Auth::user()->TenantId)->get();
        return view('company_portal.category.category_list')->with('categories', $categories ?? []);
    }
    public function editCategory($id)
    {
        $category = Category::find(Crypt::decryptString($id));
        if(!is_null($category))
        {
            return view('company_portal.category.edit_category')->with('category', $category);
        }
        else
        {
            return redirect()->back()->with('error', 'No category found');
        }
    }
    public function updateCategory(Request $request)
    {
        $this->validate($request, [
            'category' => 'required|string|min:3|max:60',
            'description' => 'required|string|max:150'
        ],
        [
            'category.required' => 'Category name is required',
            'category.string' => 'Category name must be a string',
            'category.min' => 'Category name must have at least 5 characters',
            'category.max' => 'Category name should not exceed 60 characters',
            'description.required' => 'Category description is required',
            'description.string' => 'Category description must be a string',
            'description.max' => 'Category description should not exceed 150 characters'
        ]);
        $category = Category::find(Crypt::decryptString($request->id));
        if(!is_null($category))
        {
            $category->name = $request->category;
            $category->description = $request->description;
            $category->save();
            return redirect()->route('category.list')->with('success', 'Category has been updated');
        }
        else
        {
            return redirect()->back()->with('error', 'No category found');
        }
    }
    public function deleteCategory(Request $request)
    {
        try
        {
            $category = Category::with("sitesAccount")->find(Crypt::decryptString($request->id));
            if (!is_null($category))
            {
                if($category->sitesAccount->count() > 0)
                {
                    return redirect()->back()->with('error', 'Category cannot be delete because some of its information is still being used');
                }
                else
                {
                    $category->delete();
                    return redirect()->route('category.list')->with('success', 'Category has been deleted');
                }
            }
            else
            {
                return redirect()->back()->with('error', 'No category found');
            }
        }
        catch (\Exception $exception)
        {
            Log::error('DELETE CATEGORY    '.$exception->getMessage());
            return redirect()->back()->with('error', 'No category found');
        }
    }
}
