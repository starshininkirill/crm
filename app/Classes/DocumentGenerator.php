<?php

namespace App\Classes;

use App\Models\Client;
use App\Models\Option;
use App\Models\Service;
use App\Helpers\TextFormaterHelper;
use App\Models\Organization;
use App\Models\ServiceCategory;
use Illuminate\Support\Facades\Http;

class DocumentGenerator
{
    public static function generatePaymentDocument(array $data)
    {
        $result = [
            'link' => '',
            'document' => ''
        ];

        if ($data['client_type'] == Client::TYPE_INDIVIDUAL) {
            $paymentDirection = $data['payment_direction'];
            if ($paymentDirection == 0) {
                $terminal = Organization::where('nds', Organization::WITH_NDS)->first()->terminal;
            } else {
                $terminal = 1;
            }

            $link = self::generatePaymentLink(
                $data['amount_summ'],
                $data['client_fio'],
                $data['number'],
                $data['phone'],
                $terminal
            );

            $result['link'] = $link;
        } elseif ($data['client_type'] == Client::TYPE_LEGAL_ENTITY) {
            $template_id = Organization::where('id', $data['payment_type'])->first()->template ?? null;

            if($template_id == null){
                $template_id = Option::where('name', 'payment_generator_default_law_template')->first()->value ?? 0;
            }


        }

        return $result;
    }

    public static function generateDealDocument(array $data)
    {
        $result = Bitrix::generateDealDocument($data);

        return $result;
    }

    private static function generatePaymentLink(int $amount, string $name, string $desc, string $phone, int $terminal): string
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
