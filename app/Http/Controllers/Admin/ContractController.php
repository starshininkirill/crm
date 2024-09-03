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
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

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
        $contracts = Contract::orderByDesc('created_at')->get();
        return view('admin.contract.index', ['contracts' => $contracts]);
    }

    public function create()
    {
        $services = Service::all();
        return view('admin.contract.create', ['services' => $services]);
    }


    public function store(ContractRequest $request)
    {
        try {
            DB::beginTransaction();
            $data = $request->validated();
            $user = $request->user();

            $client = Client::create($request->storeClient());

            $contractData = $request->storeContract();
            $contractData['client_id'] = $client->id;

            $createdContract = Contract::create($contractData);

            if ($createdContract) {
                $this->contractService->addPaymentsToContract($createdContract, $data['payments']);
                $this->contractService->attachPerformer($createdContract, $user->id, RoleInContract::IS_SALLER);

                $services = Service::whereIn('id', $data['service'])->get();
                $totalServicesPrice = $services->sum('price');

                if ($contractData['amount_price'] != $totalServicesPrice) {
                    throw ValidationException::withMessages(['error' => 'Цены не совпадают']);
                }

                $this->contractService->attachServices($createdContract, $services);
            }

            DB::commit();
        } catch (ValidationException $exeption) {
            DB::rollBack();
            $message = $exeption->getMessage() != '' ? $exeption->getMessage() : 'Ошибка при созданни договора';
            return back()->withErrors($message)->withInput();
        } catch (Exception $exeption) {
            DB::rollBack();
            return back()->withErrors('Ошибка при созданни договора')->withInput();
        }
        return back()->with('success', 'Договор успешно создан');
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
