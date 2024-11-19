<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ContractStoreRequest;
use App\Models\Client;
use App\Models\ContractUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Exception;

class ContractController extends Controller
{

    public function store(ContractStoreRequest $request)
    {
        try {
            DB::beginTransaction();

            $client = Client::create($request->storeClient());
            $contract = $client->contracts()->create($request->storeContract());

            $contract->addPayments($request->payments());
            $contract->attachPerformer($request->user()->id, ContractUser::SALLER);
            $contract->attachServices($request->services());

            DB::commit();
        } catch (ValidationException $exeption) {
            DB::rollBack();
            $message = $exeption->getMessage() != '' ? $exeption->getMessage() : 'Ошибка при созданни договора';
            return back()->withErrors($message)->withInput();

        } catch (Exception $exeption) {            
            DB::rollBack();
            dd($exeption);
            return back()->withErrors('Ошибка при созданни договора')->withInput();
        }

        return back()->with('success', 'Договор успешно создан');
    }
}
