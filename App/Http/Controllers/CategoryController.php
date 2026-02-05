<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Category;
use Illuminate\Support\Str;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Cache;

class CategoryController extends Controller
{

    public function index()
    {
        $with = request()->query('with');

        if ($with == 'parent') {

            $data = Category::withParent()->get();
        } elseif ($with == 'childs') {

            $data = Category::withChilds()->get();
        } else {

            $data = Category::all();
        }

        return ApiResponse::success($data);
    }
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|unique:categories',
            'images' => $request->category_id ? 'nullable' : 'required'
        ]);

        DB::beginTransaction();

        try {
            $path = public_path('/upload/images');

            if (!File::exists($path)) {
                File::makeDirectory($path, 0755, true, true);
            }

            $category = new Category();
            $category->title = $request->title;
            $category->category_id = $request->category_id ?? NULL;
            $category->slug = Str::slug($request->title);
            $category->is_front = $request->boolean('is_front');
            $category->weight = $request->weight;
            $category->created_at = now();

            $category->description = $request->description;
            $category->save();


            if ($file = $request->file('images')) {

                $imageName = Str::random(40) . '.' . $file->extension();

                $file->move($path, $imageName);

                $category->assets()->create([
                    'filename' => $imageName,
                ]);
            }
            if ($file = $request->file('banner')) {

                $imageName = Str::random(41) . '.' . $file->extension();

                $file->move($path, $imageName);

                $category->assets()->create([
                    'filename' => $imageName,
                    'variable' => 'banner'
                ]);
            }

            DB::commit();

            Cache::forget('categories');
            Cache::forget('initial_products');

            return ApiResponse::success($category);
        } catch (Exception $e) {

            DB::rollBack();

            return ApiResponse::failed($e->getMessage());
        }
    }
    public function show($id)
    {
        $data = Category::find($id)->load('image', 'banner');
        return ApiResponse::success($data);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'title' => 'required',
            'weight' => 'required',
        ]);


        DB::beginTransaction();

        try {
            $category = Category::find($id);
            $category->title = $request->title;
            $category->category_id = $request->category_id ?? NULL;
            $category->is_front = $request->boolean('is_front');
            $category->weight = $request->weight;
            $category->description = $request->description;
            $category->updated_at = now();

            $category->save();

            if ($file = $request->file('images')) {

                if ($category->image) {

                    File::delete('upload/images/' . $category->image->filename);

                    $category->image()->delete();
                }

                $imageName = Str::random(39) . '.' . $file->extension();

                $file->move(public_path('/upload/images'), $imageName);

                $category->assets()->create([
                    'filename' => $imageName,
                ]);
            }
            if ($request->boolean('remove_banner')) {

                if ($category->banner) {
                    File::delete('upload/images/' . $category->banner->filename);
                    $category->banner()->delete();
                }
            }

            if ($fileBanner = $request->file('banner')) {

                if ($category->banner) {
                    File::delete('upload/images/' . $category->banner->filename);
                    $category->banner()->delete();
                }

                $bannerName = Str::random(41) . '.' . $fileBanner->extension();

                $fileBanner->move(public_path('/upload/images'), $bannerName);

                $category->assets()->create([
                    'filename' => $bannerName,
                    'variable' => 'banner'
                ]);
            }

            if ($request->category_id) {
                $category->childs()->delete();
            }

            DB::commit();

            Cache::forget('categories');
            Cache::forget('initial_products');

            return ApiResponse::success($category);
        } catch (Exception $e) {
            DB::rollBack();

            return ApiResponse::failed($e->getMessage());
        }
    }

    public function destroy($id)
    {
        $category = Category::find($id);

        $category->childs()->delete();

        if ($category->image) {

            File::delete('upload/images/' . $category->image->filename);
        }

        if ($category->banner) {

            File::delete('upload/images/' . $category->banner->filename);
        }

        $category->assets()->delete();

        $category->delete();

        Cache::forget('categories');
        Cache::forget('initial_products');

        return ApiResponse::success();
    }
}
