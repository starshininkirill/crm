<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContractRequest;
use App\Models\Client;
use App\Models\Contract;
use App\Models\Payment;
use App\Models\RoleInContract;
use App\Models\Service;
use App\Services\ContractService;
use Illuminate\Http\Request;

class ContractController extends Controller
{
    protected $contractService;

    public function __construct(
        ContractService $contractService,
    ) {
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

        $client = Client::create($request->storeClient());

        $contractData = $request->storeContract($client);
        $createdContract = Contract::create($contractData);

        $user->contracts()->attach($createdContract->id, [
            'role_in_contracts_id' => RoleInContract::where('is_saller', RoleInContract::IS_SALLER)->value('id'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);


        // TODO Сделать цену из формы, а не из услуги
        $services = Service::whereIn('id', $data['service'])->get();
        if(!empty($services)){
            foreach ($services as $service) {
                $createdContract->services()->attach($service->id, [
                    'price' => $service->price
                ]);
            }
        }

        if ($createdContract) {
            $this->contractService
                ->addPaymentsToContract($createdContract, $data['payments']);
        }

        return redirect()->back()->with('success', 'Договор успешно создан');
    }

    public function attachUser(Request $request, Contract $contract)
    {
        $validated = $request->validate([
            'user_id' => 'required|integer|exists:users,id',
            'role_in_contracts_id' => 'required|integer|exists:role_in_contracts,id',
        ]);

        $is_created = $this->contractService->attachPerformer($contract, $validated['user_id'], $validated['role_in_contracts_id']);

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
        $performersData = $this->contractService->getPerformers($contract);
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
