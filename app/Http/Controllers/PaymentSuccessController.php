<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentSuccessController extends Controller
{
    public function handle(Request $request)
    {
        $sessionId = $request->query('session_id');

        return view('payment.success', [
            'sessionId' => $sessionId
        ]);
    }
}
