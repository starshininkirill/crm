<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Contract;
use App\Models\Payment;
use App\Models\Service;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ContractController extends Controller
{

    public function index()
    {
        $contracts = Contract::orderByDesc('created_at')->get();

        $contracts = $contracts->map(function ($contract) {
            return [
                'id' => $contract->id,
                'number' => $contract->number,
                'created_at' => $contract->created_at->format('d.m.Y'),
                'saller' => $contract->saller() ?? [],
                'parent' => $contract->parent ?? [],
                'client' => $contract->client ?? [],
                'phone' => $contract->phone ?? '',
                'services' => $contract->services ?? [],
                'price' => $contract->getPrice(),
                'payments' => $contract->payments->map(function ($payment) {
                    return [
                        'id' => $payment->id,
                        'value' => $payment->getFormatValue(),
                        'status' => $payment->status
                    ];
                }),
            ];
        })->toArray();

        return Inertia::render('Admin/Contract/Index', [
            'contracts' => $contracts,
            'paymentStatuses' => Payment::vueStatuses(),
        ]);
    }

    /**
     * Display the specified resource.
     */
    public function show(Contract $contract)
    {
        return Inertia::render('Admin/Contract/Show', [
            'contract' => [
                'id' => $contract->id,
                'number' => $contract->number,
                'price' => $contract->getPrice(),
                'services' => $contract->services->map(function($service){
                    return [
                        'id' => $service->id,
                        'name' => $service->name,
                        'price' => $service->getPrice(),
                    ];
                }),
                'payments' => $contract->payments,
            ],
        ]);

        // $performersData = $contract->getPerformers($contract);
        
        // return view('admin.contract.show', compact('contract', 'performersData'));
    }

    public function attachUser(Request $request, Contract $contract)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'role_in_contracts_id' => 'required|integer|exists:role_in_contracts,id',
        ]);

        $is_created = $contract->attachPerformer($validated['user_id'], $validated['role_in_contracts_id']);

        if ($is_created) {
            return redirect()->back()->with('success', 'Исполнитель успешно добавлен.');
        } else {
            return redirect()->back()->withErrors(['user_id' => 'Пользователь уже привязан к данной роли.']);
        }
    }
}
