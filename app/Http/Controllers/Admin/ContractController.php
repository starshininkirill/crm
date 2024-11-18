<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;

use App\Models\Contract;
use App\Models\Payment;
use App\Models\Service;
use Illuminate\Http\Request;

class ContractController extends Controller
{

    public function index()
    {
        $contracts = Contract::orderByDesc('created_at')->get();

        return view('admin.contract.index', [
            'contracts' => $contracts,
            'paymentClass' => Payment::class, 
        ]);
    }

    public function create()
    {
        $services = Service::all();
        return view('admin.contract.create', ['services' => $services]);
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

    /**
     * Display the specified resource.
     */
    public function show(Contract $contract)
    {
        $performersData = $contract->getPerformers($contract);
        return view('admin.contract.show', compact('contract', 'performersData'));
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
