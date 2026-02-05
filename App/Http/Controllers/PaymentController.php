<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Order;

class PaymentController extends Controller
{
    public function uploadProof(Request $request)
    {
        $request->validate([
            'order_ref' => 'required|exists:orders,order_ref',
            'proof' => 'required|image|mimes:jpg,jpeg,png|max:2048', 
        ]);

        $order = Order::where('order_ref', $request->order_ref)->firstOrFail();

        $uploadPath = public_path('upload/bukti_tf');
        if (!file_exists($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        $file = $request->file('proof');
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move($uploadPath, $filename);


        $order->bukti_tf = 'upload/bukti_tf/' . $filename;
        $order->save();

        return response()->json([
            'message' => 'Bukti transfer berhasil diupload',
            'bukti_tf' => url($order->bukti_tf) 
        ]);
    }
}
