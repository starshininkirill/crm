<?php

namespace App\Classes;

use App\Models\Client;
use App\Models\Option;
use App\Models\Service;
use App\Helpers\TextFormaterHelper;
use App\Models\Organization;
use App\Models\ServiceCategory;
use Illuminate\Support\Facades\Http;

class Bitrix
{

    public static function generatePaymentDocument(array $data)
    {

        $bitrixData = self::preparePaymentData($data);

        $response = Http::withOptions(["verify" => false])->asForm()->post('https://automatization.grampus-server.ru/actions/wiki/generateDocument/index2.php', $bitrixData);
        
        if ($response->status() == 200) {
            $result = $response->json();
        } else {
            $result = [];
        }

        return $result;
    }

    private static function preparePaymentData(array $data): array
    {
        $template_id = Organization::where('id', $data['payment_type'])->first()->template ?? null;

        if ($template_id == null) {
            $template_id = Option::where('name', 'payment_generator_default_law_template')->first()->value ?? 0;
        }

        $array_total_summ = TextFormaterHelper::visualFormatNumber($data['act_payment_summ'], true, false);
        $bitrixData = [
            'template_id' => $template_id,
            'lead_id' => $data['leed'],
            'with_pdf' => true,
            'physics' => Client::translateType($data['client_type']),
            'deal_id' => $data['deal_id'],
            'client_phone' => '',
            'crm_fields' => [
                'deal_number' => $data['number'],
                'amount' => '',
                'client_phone' => '',
                'UF_CRM_1671028363' => '', // ФИО
                'UF_CRM_1671028759' => $data['organization_short_name'],
                'UF_CRM_1671028872' => $data['legal_address'],
                'UF_CRM_1671028881' => $data['inn'],
                'UF_CRM_1711519450' => TextFormaterHelper::visualFormatNumber($data['act_payment_summ'], true, true),
                'UF_CRM_1711519550' => $data['act_payment_goal'],
                'UF_CRM_1722077889' => $array_total_summ['text'] . ' ' . $array_total_summ['ruble'],
            ],
        ];

        return $bitrixData;
    }

    public static function generateDealDocument(array $data)
    {
        $bitrixData = self::prepareDealData($data);
        $response = Http::withOptions(["verify" => false])->asForm()->post('https://automatization.grampus-server.ru/actions/wiki/generateDocument/index2.php', $bitrixData);

        if ($response->status() == 200) {
            $result = $response->json()['download_link'];
        } else {
            $result = '';
        }


        return $result;
    }

    private static function prepareDealData(array $data): array
    {

        $result = [];

        // Временные данные
        $isIndividual = self::validKeyInArray($data, 'client_type') && $data['client_type'] == Client::TYPE_INDIVIDUAL;
        $isNds = self::validKeyInArray($data, 'tax') && $data['tax'] == 1;
        $isOgrn = self::validKeyInArray($data, 'register_number_type') && $data['register_number_type'] == 0;

        // Номер лида
        if (self::validKeyInArray($data, 'leed')) {
            $result['lead_id'] = $data['leed'];
        }

        // Контактный телефон
        if (self::validKeyInArray($data, 'contact_phone')) {
            $result['client_phone'] = $data['contact_phone'];
        }

        // Номер Договора
        if (self::validKeyInArray($data, 'number')) {
            $result['crm_fields']['UF_CRM_1671028945'] = $data['number'];
        }

        // Кол-во страниц СЕО
        if (self::validKeyInArray($data, 'seo_pages')) {
            $result['crm_fields']['UF_CRM_1700118214'] = $data['seo_pages'];
        }

        // Описание для РК
        if (self::validKeyInArray($data, 'rk_text')) {
            $result['crm_fields']['UF_CRM_1671029036'] = $data['rk_text'];
        }

        // Ссылка на готовый дизайн
        if (self::validKeyInArray($data, 'ready_site_link')) {
            $result['crm_fields']['UF_CRM_1640600338'] = $data['ready_site_link'];
        }

        // Файл готового дизайна в base64
        if (self::validKeyInArray($data, 'ready_site_image')) {
            $result['crm_fields']['crm_files']['UF_CRM_1642753651'] = $data['ready_site_image'];
        }

        // ФИО представителя и номер
        if (self::validKeyInArray($data, 'contact_fio') && self::validKeyInArray($data, 'contact_phone')) {
            $result['crm_fields']['UF_CRM_1685186412058'] = $data['contact_fio'] . ' ' . $data['contact_phone'];
        }


        self::generatePaymentsData($data, $result);

        self::generateClientData($data, $result, $isIndividual, $isOgrn, $isNds);

        self::generateMainService($data, $result, $isIndividual);

        self::generateServiceFields($data, $result);

        return $result;
    }

    private static function generatePaymentsData(array $data, &$result): void
    {
        // Итоговая сумма
        if (self::validKeyInArray($data, 'amount_price')) {
            $result['crm_fields']['UF_CRM_1671029365'] = TextFormaterHelper::visualFormatNumber($data['amount_price'], true, true);
        }

        // Скидка
        if (self::validKeyInArray($data, 'sale')) {
            $result['crm_fields']['UF_CRM_1671029351'] = TextFormaterHelper::visualFormatNumber($data['sale'], true, true);
        }

        // Общий срок разработки
        if (self::validKeyInArray($data, 'development_time')) {
            $result['crm_fields']['UF_CRM_1671029378'] = TextFormaterHelper::visualFormatDeadline($data['development_time']);
        }


        if (empty($data['payments'])) {
            return;
        }

        $payments = $data['payments'];

        $paymentsBitrixFields = [
            'UF_CRM_1640601250',
            'UF_CRM_1640601264',
            'UF_CRM_1640601276'
        ];

        foreach ($payments as $key => $value) {
            $result['crm_fields'][$paymentsBitrixFields[$key]] = TextFormaterHelper::visualFormatNumber($value, false, true);
        }
    }

    private static function generateMainService(array $data, &$result, $isIndividual): void
    {
        if (empty($data['services'])) {
            return;
        }
        $serviceId = $data['services'][0]['service_id'];
        $price = $data['services'][0]['price'];

        $service = Service::where('id', $serviceId)->first();

        // Цена главной услуги
        $result['crm_fields']['UF_CRM_1671028990'] = TextFormaterHelper::visualFormatNumber($data['services'][0]['price'], true, true);

        // Устанавливаем ID шаблона
        $result['template_id'] = $service->dealTemplateId($isIndividual, count($data['services']) == 1);


        // Добавляем текст для оплат
        if (self::validKeyInArray($result['crm_fields'], 'UF_CRM_1640601264')) {
            $avance_text = '';
            if ($service->category->type == ServiceCategory::READY_SITE) {
                $avance_text = "1.2 По окончанию работ по разработке сайта Заказчик перечисляет " . TextFormaterHelper::visualFormatNumber($result['crm_fields']['UF_CRM_1640601264'], true, true) . " 00 копеек на расчетный счет Исполнителя на основании выставленного счета в течении 3-х рабочих дней";
            } else {
                /*-------- проверка на наличие 3 аванса ------------*/
                if (self::validKeyInArray($result['crm_fields'], 'UF_CRM_1640601276')) {
                    $avance_text = "1.2 После согласования дизайна сайта Заказчик перечисляет оплату в размере " . TextFormaterHelper::visualFormatNumber($result['crm_fields']['UF_CRM_1640601264'], true, true) . " 00 копеек на расчетный счет Исполнителя на основании выставленного счета в течении 3-х рабочих дней.";
                } else {
                    $avance_text = "1.2 По окончанию работ по разработке сайта Заказчик перечисляет " . TextFormaterHelper::visualFormatNumber($result['crm_fields']['UF_CRM_1640601264'], true, true) . " 00 копеек на расчетный счет Исполнителя на основании выставленного счета в течении 3-х рабочих дней";
                }
            }
            $result['crm_fields']['UF_CRM_1724337871'] = $avance_text;
        }
        /*----------- 3 аванс ----------*/
        if (self::validKeyInArray($result['crm_fields'], 'UF_CRM_1640601276')) {
            $avance_text = '';
            if ($service->category->type != ServiceCategory::READY_SITE) {
                $avance_text = "1.3 По окончанию работ по разработке сайта Заказчик перечисляет " . TextFormaterHelper::visualFormatNumber($result['crm_fields']['UF_CRM_1640601276'], true, true) . " 00 копеек на расчетный счет Исполнителя на основании выставленного счета в течении 3-х рабочих дней";
            }
            $result['crm_fields']['UF_CRM_1724337878'] = $avance_text;
        }
    }

    private static function generateServiceFields(array $data, &$result): void
    {
        if (empty($data['services'])) {
            return;
        }

        $services = $data['services'];

        // Получение услуг из базы данных
        $serviceIds = collect($services)->pluck('service_id');
        $dbServices = Service::whereIn('id', $serviceIds)->with('category')->get()->keyBy('id');

        // Объединение массивов
        $mergedServices = collect($services)->map(function ($service) use ($dbServices) {
            $dbService = $dbServices->get($service['service_id']);
            return array_merge($service, [
                'name' => $dbService->name ?? null,
                'type' => $dbService->category->type ?? null,
                'description' => $dbService->description ?? null,
                'duration' => $dbService->work_days_duration,
            ]);
        });

        $mergedServices = $mergedServices->map(function ($item) {
            unset($item['service_id']);
            return $item;
        })->toArray();

        $additional_services = [
            1 => [
                'name' => 'UF_CRM_1712130712',
                'price' => 'UF_CRM_1671029057',
                'duration' => 'UF_CRM_1671029066',
                'description' => 'UF_CRM_1671029073'
            ],
            2 => [
                'name' => 'UF_CRM_1712130734',
                'price' => 'UF_CRM_1671029090',
                'duration' => 'UF_CRM_1671029108',
                'description' => 'UF_CRM_1671029119'
            ],
            3 => [
                'name' => 'UF_CRM_1712130746',
                'price' => 'UF_CRM_1671029322',
                'duration' => 'UF_CRM_1671029335',
                'description' => 'UF_CRM_1671029342'
            ],
            4 => [
                'name' => 'UF_CRM_1712130768',
                'price' => 'UF_CRM_1712130835',
                'duration' => 'UF_CRM_1712130843',
                'description' => 'UF_CRM_1712130851'
            ],
            5 => [
                'name' => 'UF_CRM_1712130866',
                'price' => 'UF_CRM_1712130896',
                'duration' => 'UF_CRM_1712130903',
                'description' => 'UF_CRM_1712130910'
            ],
        ];

        foreach ($mergedServices as $key => $service) {
            if (isset($additional_services[$key])) {
                foreach ($service as $field => $value) {
                    if (isset($additional_services[$key][$field])) {
                        $newFieldName = $additional_services[$key][$field];
                        if ($field == 'duration') {
                            $value = $service['duration'] ?? TextFormaterHelper::visualFormatDeadline($value);
                        }
                        if ($field == 'price') {
                            $value = TextFormaterHelper::visualFormatNumber($value, true, true);
                        }
                        if ($field == 'name' && $service['type'] == ServiceCategory::SEO && array_key_exists('UF_CRM_1700118214', $result['crm_fields'])) {
                            $value =  $value . ' (' . $result['crm_fields']['UF_CRM_1700118214'] . ' страниц)';
                        }
                        $mergedServices[$key][$newFieldName] = $value;
                        unset($mergedServices[$key][$field]);
                    }
                }
            }
        }


        unset($mergedServices[0]);
        $mergedServices = array_values($mergedServices);

        foreach ($mergedServices as $service) {
            $result['crm_fields'] = [...$result['crm_fields'], ...$service];
        }

        $maybeEmptyFields = [
            'UF_CRM_1712130712',
            'UF_CRM_1671029057',
            'UF_CRM_1671029066',
            'UF_CRM_1671029073',
            'UF_CRM_1712130734',
            'UF_CRM_1671029090',
            'UF_CRM_1671029108',
            'UF_CRM_1671029119',
            'UF_CRM_1712130746',
            'UF_CRM_1671029322',
            'UF_CRM_1671029335',
            'UF_CRM_1671029342',
            'UF_CRM_1712130768',
            'UF_CRM_1712130835',
            'UF_CRM_1712130843',
            'UF_CRM_1712130851',
            'UF_CRM_1712130866',
            'UF_CRM_1712130896',
            'UF_CRM_1712130903',
            'UF_CRM_1640601264',
            'UF_CRM_1712130910',
            'UF_CRM_1640601276',
            'UF_CRM_1724337871',
            'UF_CRM_1724337878',
            'UF_CRM_1671029036',
            'UF_CRM_1700118214',
            'UF_CRM_1671028801',
            'UF_CRM_1671029036'
        ];

        foreach ($maybeEmptyFields as $key => $field) {
            if (!array_key_exists($field, $result['crm_fields'])) {
                $result['crm_fields'][$field] = ' ';
            }
        }
    }

    private static function generateClientData($data, &$result, $isIndividual, $isOgrn, $isNds): void
    {
        // Данные контрагента
        // Физик
        if ($isIndividual) {
            if (self::validKeyInArray($data, 'client_fio')) {
                $result['crm_fields']['UF_CRM_1671028363'] = $data['client_fio'];
            }
            if (self::validKeyInArray($data, 'passport_series')) {
                $result['crm_fields']['UF_CRM_1671028380'] = $data['passport_series'];
            }
            if (self::validKeyInArray($data, 'passport_number')) {
                $result['crm_fields']['UF_CRM_1671028398'] = $data['passport_number'];
            }
            if (self::validKeyInArray($data, 'passport_issued')) {
                $result['crm_fields']['UF_CRM_1671028412'] = $data['passport_issued'];
            }
            if (self::validKeyInArray($data, 'physical_address')) {
                $result['crm_fields']['UF_CRM_1671028419'] = $data['physical_address'];
            }
        }
        //Юр лицо
        else {
            if (self::validKeyInArray($data, 'organization_name')) {
                $result['crm_fields']['UF_CRM_1671028429'] = $data['organization_name'];
            }
            if (self::validKeyInArray($data, 'organization_short_name')) {
                $result['crm_fields']['UF_CRM_1671028759'] = $data['organization_short_name'];
            }
            if (self::validKeyInArray($data, 'register_number')) {
                $result['crm_fields']['UF_CRM_1671028783'] = $data['register_number'];
            }
            if (self::validKeyInArray($data, 'director_name') && !$isOgrn) {
                $result['crm_fields']['UF_CRM_1671028801'] = 'В лице генерального директора ' . $data['director_name'] . ' действующего на основании устава.';
            }
            if (self::validKeyInArray($data, 'legal_address')) {
                $result['crm_fields']['UF_CRM_1671028872'] = $data['legal_address'];
            }
            if (self::validKeyInArray($data, 'inn')) {
                $result['crm_fields']['UF_CRM_1671028881'] = $data['inn'];
            }
            if (self::validKeyInArray($data, 'current_account')) {
                $result['crm_fields']['UF_CRM_1671028889'] = $data['current_account'];
            }
            if (self::validKeyInArray($data, 'correspondent_account')) {
                $result['crm_fields']['UF_CRM_1671028897'] = $data['correspondent_account'];
            }
            if (self::validKeyInArray($data, 'bank_name')) {
                $result['crm_fields']['UF_CRM_1671028905'] = $data['bank_name'];
            }
            if (self::validKeyInArray($data, 'bank_bik')) {
                $result['crm_fields']['UF_CRM_1671028914'] = $data['bank_bik'];
            }

            // Данные для заполненея счёта и акта
            if (self::validKeyInArray($data, 'act_payment_summ')) {
                $result['crm_fields']['UF_CRM_1711519450'] = TextFormaterHelper::visualFormatNumber($data['act_payment_summ'], true, true);

                if ($isNds) {
                    $ndsInstance = Option::where('name', 'tax_nds')->first();
                    $nds = $ndsInstance != null ? intval($ndsInstance->value) : 20;
                    $ndsDecimal = $nds / 100;
                    $ndsAmount = intval((intval($data['act_payment_summ']) / (1 + $ndsDecimal)) * $ndsDecimal);
                    $result['crm_fields']['UF_CRM_1723109815'] = TextFormaterHelper::visualFormatNumber($ndsAmount, true, true);
                }
            }
            if (self::validKeyInArray($data, 'act_payment_goal')) {
                $result['crm_fields']['UF_CRM_1711519550'] = $data['act_payment_goal'];
            }
        }
    }
    private static function validKeyInArray(array $data, string $key)
    {
        return array_key_exists($key, $data) && $data[$key] != '';
    }
}
