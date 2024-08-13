<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContractRequest;
use App\Models\Client;
use App\Models\Contract;
use App\Models\Payment;
use App\Models\Service;
use App\Services\ContractService;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    protected $contractService;

    public function __construct(ContractService $contractService)
    {
        $this->contractService = $contractService;
    }

    public function index()
    {

        $contracts = Contract::all();

        return view('admin.contract.index', ['contracts' => $contracts]);

    }

    public function create()
    {
        $services = Service::all(); 
        return view('admin.contract.create', ['services' => $services]);
    }


    public function store(ContractRequest $request)
    {
        $client = Client::create($request->getClientData());

        $contractData = $request->storeContract();
        $contractData['client_id'] = $client->id;
        $contractData['user_id'] = auth()->user()->id;

        $createdContract = Contract::create($contractData);

        $services = $request->input('service', []);
        if ($services) {
            $createdContract->services()->sync($services);
        }

        if ($createdContract) {
            $this->contractService->addPaymentsToContract($createdContract, $request->input('payments', []));
        }

        return redirect()->back()->with('success', 'Договор успешно создан');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $contract = Contract::findOrFail($id);

        return view('admin.contract.show', ['contract' => $contract]);
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
