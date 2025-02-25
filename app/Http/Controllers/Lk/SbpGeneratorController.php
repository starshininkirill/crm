<?php

namespace App\Http\Controllers\Lk;

use App\Http\Controllers\Controller;
use App\Http\Requests\Lk\SbpGeneratorRequest;
use App\Models\Organization;
use App\Models\Payment;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class SbpGeneratorController extends Controller
{
    public function create()
    {
        $organizations = Organization::all();
        return Inertia::render('Lk/SbpPayment/Create', [
            'organizations' => $organizations,
        ]);
    }

    public function store(SbpGeneratorRequest $request)
    {
        $validated = $request->validated();

        if (!$request->hasFile('file')) {
            return redirect()->back()->withErrors('Не удалось загрузить файл');
        }

        $file = $request->file('file');
        $uploadedFile = $file->store('paymentChecks', 'public');
        $fileUrl = Storage::url($uploadedFile);

        $validated['receipt_url'] = $fileUrl;
        $validated['status'] = Payment::STATUS_WAIT_CONFIRMATION; 
        $validated['description'] = 'Договор: ' . $validated['number'] . '. ' . $validated['description'];

        Payment::create($validated);

        return redirect()->back()->with('success', 'Платёж СБП успешно создан');
    }
}
