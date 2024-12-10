<?php

namespace App\Http\Controllers\Resources;

use App\Classes\Bitrix;
use App\Classes\DocumentGenerator;
use App\Http\Controllers\Controller;
use App\Http\Requests\ContractStoreRequest;
use App\Models\Client;
use App\Models\ContractUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class ContractController extends Controller
{

    public function store(ContractStoreRequest $request)
    {
        $data = $request->validated();
        
        if ($request->hasFile('ready_site_image')) {
            $file = $request->file('ready_site_image');
        
            $fileContent = $file->getContent();
            $base64String = base64_encode($fileContent);
            $mimeType = $file->getMimeType();
        
            $fullBase64String = 'data:' . $mimeType . ';base64,' . $base64String;
        
            $data['ready_site_image'] = $fullBase64String;
        }

        $link = DocumentGenerator::generateDealDocument($data);

        if(empty($link) || $link == ''){
            return back()->withErrors('Ошибка Создания договора в Bitrix')->withInput();
        };

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

            Log::channel('errors')->error('Validation error when creating a contract', [
                'message' => $exeption->getMessage(),
                'request_data' => $request->all(),
                'trace' => $exeption,
            ]);

            $message = $exeption->getMessage() != '' ? $exeption->getMessage() : 'Ошибка при созданни договора';
            return back()->withErrors($message)->withInput();

        } catch (Exception $exeption) {            
            DB::rollBack();

            Log::channel('errors')->error('Unexpected error when creating a contract', [
                'message' => $exeption->getMessage(),
                'request_data' => $request->all(),
                'trace' => $exeption,
            ]);

            return back()->withErrors('Ошибка при созданни договора')->withInput();
        }

        return back()->with('link', $link);
    }
}
