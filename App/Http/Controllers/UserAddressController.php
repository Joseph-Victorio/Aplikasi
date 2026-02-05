<?php

namespace App\Http\Controllers;

use App\Helpers\ApiResponse;
use App\Models\UserAddress;
use Illuminate\Http\Request;

class UserAddressController extends Controller
{
    public function index()
    {
        $data =  UserAddress::where('user_id', request()->user()->id)->get();

        return ApiResponse::success($data);
    }
    public function update(Request $request, $id)
    {
        $request->validate([
            'label' => 'required',
            'address' => 'required',
        ]);
        
        
        if($request->boolean('is_primary') == true) {
            UserAddress::where('user_id', $request->user()->id)->update(['is_primary' => false]);
        }
            
        $userAddress = UserAddress::findOrFail($id);

        $userAddress->label = $request->label;
        $userAddress->address = $request->address;
        $userAddress->is_primary = $request->boolean('is_primary') ? true : false;
        $userAddress->save();

        return ApiResponse::success($userAddress);
    }
    public function store(Request $request)
    {
        $request->validate([
            'label' => 'required',
            'address' => 'required',
        ]);

        $user = $request->user();

       if($request->boolean('is_primary') == true) {
            UserAddress::where('user_id', $user->id)->update(['is_primary' => false]);
       }

       $userAddress = $user->address()->create([
        'label' => $request->label,
        'address' => $request->address,
        'is_primary' => $request->boolean('is_primary') ? true : false
       ]);

       return ApiResponse::success($userAddress);

    }

    public function destroy($id)
    {
       UserAddress::findOrFail($id)->delete();

       return ApiResponse::success();
    }
}
