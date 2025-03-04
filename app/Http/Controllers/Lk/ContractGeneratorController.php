<?php

namespace App\Http\Controllers\Lk;

use App\Http\Controllers\Controller;
use App\Models\Option;
use App\Models\ServiceCategory;
use App\Classes\DocumentGenerator;
use App\Http\Requests\Lk\ContractGeneratorRequest;
use App\Models\Client;
use App\Models\ContractUser;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Exception;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;

class ContractGeneratorController extends Controller
{
    public function create()
    {

        $cats = ServiceCategory::with('services')->get();
        $needSeoPages = json_decode(Option::where('name', 'contract_generator_need_seo_pages')->first()?->value, true) ?? [];

        if (!$cats->isEmpty()) {
            $catsWithServices = $cats->map(function ($category) use ($needSeoPages) {
                return [
                    'id' => $category->id,
                    'category' => $category->name,
                    'isRk' => $category->type == ServiceCategory::RK,
                    'services' => $category->services->map(function ($service) use ($needSeoPages) {
                        return [
                            'id' => $service->id,
                            'name' => $service->name,
                            'price' => $service->price,
                            'work_days_duration' => $service->numeric_working_days(),
                            'isRk' => $service->category->type == ServiceCategory::RK ? true : false,
                            'isSeo' => $service->category->type == ServiceCategory::SEO ? true : false,
                            'isReady' => $service->category->type == ServiceCategory::READY_SITE ? true : false,
                            'needSeoPages' => in_array($service->id, $needSeoPages),
                        ];
                    }),
                ];
            })->toArray();
        }

        $options = Option::whereIn('name', ['contract_generator_main_categories', 'contract_generator_secondary_categories', 'contract_generator_rk_text'])->get()->keyBy('name');

        $mainCatsIds = $options->get('contract_generator_main_categories')->value ?? '[]';
        $secondaryCatsIds = $options->get('contract_generator_secondary_categories')->value ?? '[]';
        $contractRkText = $options->get('contract_generator_rk_text')->value ?? [];

        return Inertia::render('Lk/Contract/Create', [
            'cats' => $catsWithServices ?? [],
            'mainCatsIds' => json_decode($mainCatsIds),
            'secondaryCatsIds' => json_decode($secondaryCatsIds),
            'rkText' => json_decode($contractRkText),
        ]);
    }
    public function store(ContractGeneratorRequest $request, DocumentGenerator $documentGenerator)
    {
        $data = $request->validated();

        // if ($request->hasFile('ready_site_image')) {
        //     $file = $request->file('ready_site_image');
        //     $fileContent = $file->getContent();
        //     $base64String = base64_encode($fileContent);
        //     $mimeType = $file->getMimeType();

        //     $fullBase64String = 'data:' . $mimeType . ';base64,' . $base64String;

        //     $data['ready_site_image'] = $fullBase64String;
        // }

        // $fileLink = DocumentGenerator::generateDealDocument($data);
        $terminal = 1;

        $link = '';
        if ($data['client_type'] == Client::TYPE_INDIVIDUAL) {
            $link =  $documentGenerator->generatePaymentLink(
                $data['amount_price'],
                $data['client_fio'],
                $data['number'],
                $data['contact_phone'],
                $terminal
            );
        };

        // if (empty($fileLink) || $fileLink == '') {
        //     return back()->withErrors('Ошибка Создания договора в Bitrix')->withInput();
        // };

        try {
            DB::beginTransaction();

            $client = Client::create($request->storeClient());
            $contract = $client->contracts()->create($request->storeContract());

            $contract->addPayments($request->payments());
            $contract->attachPerformer($request->user()->id, ContractUser::SALLER);
            $contract->services()->attach($request->services());

            DB::commit();
        } catch (ValidationException $exeption) {
            dd($exeption);
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

        return back()->with([
            // 'file' => $fileLink,
            // 'link' => $link
        ]);
    }
}
