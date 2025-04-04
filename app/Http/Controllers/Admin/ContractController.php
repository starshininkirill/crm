<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\TextFormaterHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\ContractRequest;
use App\Models\Contract;
use App\Models\Payment;
use App\Models\Service;
use App\Models\User;
use App\Services\ContractService;
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
                'price' => $contract->amount_price,
                'payments' => $contract->payments->map(function ($payment) {
                    return [
                        'id' => $payment->id,
                        'value' => $payment->value,
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

    public function show(Contract $contract)
    {
        $users = User::all();

        $contractData = [
            'id' => $contract->id,
            'number' => $contract->number,
            'phone' => $contract->phone,
            'price' => TextFormaterHelper::getPrice($contract->amount_price),
            'sale' => TextFormaterHelper::getPrice($contract->sale ?? 0),
            'created_at' => $contract->created_at->format('d.m.Y'),
            'parent' => $contract->parent ?? '',
            'services' => $contract->services->map(function ($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    'price' => TextFormaterHelper::getPrice($service->pivot->price),
                ];
            }),

            'payments' => $contract->payments->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'order' => $payment->order,
                    'value' =>  TextFormaterHelper::getPrice($payment->value),
                    'is_close' => $payment->status == Payment::STATUS_CLOSE,
                ];
            }),
            'client' => $contract->client,
            'performers' => $contract->getPerformers(),
        ];

        return Inertia::render('Admin/Contract/Show', [
            'contract' => $contractData,
            'users' => $users,
        ]);
    }

    public function attachPerformers(ContractRequest $request, Contract $contract, ContractService $service)
    {
        $service->attachPerformers($contract, $request->getPerformersData());

        return redirect()->back()->with('success', 'Исполнители успешно изменены');
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
