<?php

namespace App\Classes;

use App\Models\Client;
use App\Models\Option;
use App\Models\Service;
use App\Helpers\TextFormaterHelper;
use App\Models\Organization;
use App\Models\ServiceCategory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\TemplateProcessor;

class DocumentGenerator
{

    public function __construct(
        protected FileManager $fileManager
    ) {}
    // public static function generatePaymentDocument(array $data)
    // {
    //     $result = [
    //         'link' => '',
    //         'download_link' => '',
    //         'pdf_download_link' => '',
    //     ];

    //     if ($data['client_type'] == Client::TYPE_INDIVIDUAL) {
    //         $paymentDirection = $data['payment_direction'];

    //         if ($paymentDirection == 0) {
    //             $terminal = Organization::where('nds', Organization::WITH_NDS)->first()->terminal;
    //         } else {
    //             $terminal = 1;
    //         }

    //         $link = self::generatePaymentLink(
    //             $data['amount_summ'],
    //             $data['client_fio'],
    //             $data['number'],
    //             $data['phone'],
    //             $terminal
    //         );

    //         $result['link'] = $link;
    //     } elseif ($data['client_type'] == Client::TYPE_LEGAL_ENTITY) {
    //         $bitrixResponse = Bitrix::generatePaymentDocument($data);

    //         if(array_key_exists('download_link', $bitrixResponse) && $bitrixResponse['download_link'] != ''){
    //             $result['download_link'] = $bitrixResponse['download_link'];
    //         }
    //         if(array_key_exists('pdf_download_link', $bitrixResponse) && $bitrixResponse['pdf_download_link'] != ''){
    //             $result['pdf_download_link'] = $bitrixResponse['pdf_download_link'];
    //         }
    //     }

    //     return $result;
    // }

    public function generatePaymentDocument(array $data): string
    {
        $organisation = Organization::find($data['organization_id']);
        if (!$organisation) {
            return '';
        }

        // TODO 
        // Убрать documentTemplates
        $documentTemplate = $organisation->documentTemplates()->first();
        if (!$documentTemplate || !$this->fileManager->checkExist($documentTemplate->file)) {
            return '';
        }

        $filePath = Storage::path('public/' . $documentTemplate->file);

        $templateProcessor = new TemplateProcessor($filePath);

        $templateProcessor->setValue('DocumentNumber', $data['number']);
        $templateProcessor->setValue('DocumentCreateTime', Carbon::now()->format('Y.m.d'));
        $templateProcessor->setValue('act_payment_goal', $data['act_payment_goal']);
        $templateProcessor->setValue('act_payment_summ', $data['act_payment_summ']);
        $templateProcessor->setValue('nds', $data['act_payment_summ'] / 100 * 5);
        $templateProcessor->setValue('organization_short_name', $data['organization_short_name']);
        $templateProcessor->setValue('inn', $data['inn']);
        $templateProcessor->setValue('legal_address', $data['legal_address']);
        $templateProcessor->setValue('inn', $data['inn']);


        $outputRelativePath = 'generatedDocuments/document.docx';

        $templateProcessor->saveAs(storage_path('app/public/' . $outputRelativePath));

        return Storage::url($outputRelativePath);
    }

    public static function generateDealDocument(array $data)
    {
        $result = Bitrix::generateDealDocument($data);

        return $result;
    }

    public function generatePaymentLink(int $amount, string $name, string $desc, string $phone, int $terminal): string
    {
        $target_payment_link = 'https://grampus-studio.ru/oplata-uslug/';

        $payment_query = http_build_query([
            'amount' => $amount,
            'imya' => $name,
            'description' => $desc,
            'phone' => $phone,
            't' => $terminal,
        ]);

        return file_get_contents('https://clck.ru/--?url=' . urlencode($target_payment_link . '?' . $payment_query));
    }
}
