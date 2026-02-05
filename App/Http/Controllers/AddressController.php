<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Helpers\ApiResponse;
use Illuminate\Http\Request;

class AddressController extends Controller
{
    public function search($key)
    {
        $key = filter_var($key, FILTER_SANITIZE_SPECIAL_CHARS);

        $data = Address::where('name', 'like', '%' . $key . '%')
            ->get();

        return ApiResponse::success($data);
    }
}
