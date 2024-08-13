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

    public function __construct(
        ContractService $contractService,
    )
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
        $data = $request->validated();
        $user = $request->user();

        $client = Client::create($request->getClientData());

        $contractData = $request->storeContract($client);
        $createdContract = $user->contracts()->create($contractData);

        $createdContract->services()->sync($data['service']);

        if ($createdContract) {
            $this->contractService
                ->addPaymentsToContract($createdContract, $data['payments']);
        }

        return redirect()->back()->with('success', 'Договор успешно создан');
    }

    /**
     * Display the specified resource.
     */
    public function show(Contract $contract)
    {
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
