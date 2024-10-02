<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $payments = Payment::query()->where('status', Payment::STATUS_CLOSE)->get();
        return view('admin.payment.index', [
            'payments' => $payments,
            'paymentClass' => Payment::class,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function unsorted()
    {
        $payments = Payment::query()->whereNull('contract_id')
            ->whereNot('status', Payment::STATUS_CLOSE)
            ->get();
        
        return view('admin.payment.unsorted', ['payments' => $payments]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Payment $payment)
    {
        return view('admin.payment.show', ['payment' => $payment]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
