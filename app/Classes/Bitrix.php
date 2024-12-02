<?php

namespace App\Classes;

use App\Models\Client;
use App\Models\Service;

class Bitrix
{
    public static function generateDealDocument(array $data)
    {
        $bitrixData = self::prepareDealData($data);
    }

    private static function prepareDealData(array $data): array
    {

        $result = [];

        // Временные данные
        $isIndividual = self::validKeyInArray($data, 'client_type') && $data['client_type'] == Client::TYPE_INDIVIDUAL;
        $isNds = self::validKeyInArray($data, 'nds') && $data['nds'] == 1;
        $isOgrn = self::validKeyInArray($data, 'register_number_type') && $data['register_number_type'] == 0;


        $services = $data['services'];

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

        // Тут будет файл готового дизайна
        if (self::validKeyInArray($data, 'ready_site_image')) {
            $result['crm_fields']['UF_CRM_1642753651'] = $data['ready_site_image'];
        }

        // ФИО представителя и номер
        if (self::validKeyInArray($data, 'contact_fio') && self::validKeyInArray($data, 'contact_phone')) {
            $result['crm_fields']['UF_CRM_1685186412058'] = $data['contact_fio'] . ' ' . $data['contact_phone'];
        }



        self::generateClientData($data, $result, $isIndividual);

        self::generateMainService($data, $result, $isIndividual);

        self::generateServiceFields($data, $result);


        dd($data);
        return $result;
    }

    private static function generateMainService(array $data, &$result, $isIndividual)
    {
        if (empty($data['services'])) {
            return;
        }
        $serviceId = $data['services'][0]['service_id'];
        $price = $data['services'][0]['price'];

        $service = Service::where('id', $serviceId)->first();

        // Цена главной услуги
        $result['crm_fields']['UF_CRM_1671028990'] = self::visualFormatNumber($data['services'][0]['price'], true, true);

        // Устанавливаем ID шаблона
        $result['template_id'] = $service->dealTemplateId($isIndividual, count($data['services']) == 1);
    }

    private static function generateServiceFields(array $data, &$result): void
    {
        if (empty($data['services'])) {
            return;
        }

        $services = $data['services'];

        // Получение услуг из базы данных
        $serviceIds = collect($services)->pluck('service_id');
        $dbServices = Service::whereIn('id', $serviceIds)->get(['id', 'name', 'description'])->keyBy('id');

        // Объединение массивов
        $mergedServices = collect($services)->map(function ($service) use ($dbServices) {
            $dbService = $dbServices->get($service['service_id']);
            return array_merge($service, [
                'name' => $dbService->name ?? null,
                'description' => $dbService->description ?? null
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
                            $value = self::visualFormatDeadline($value);
                        }
                        if ($field == 'price') {
                            $value = self::visualFormatNumber($value, true, true);
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

        $maybe_empty_fields = [
            'UF_CRM_1712130712',
            'UF_CRM_1712130734',
            'UF_CRM_1712130746',
            'UF_CRM_1712130768',
            'UF_CRM_1712130866',
            'UF_CRM_1640601264',
            'UF_CRM_1640601276',
            'UF_CRM_1724337871',
            'UF_CRM_1724337878',
        ];

        foreach ($maybe_empty_fields as $key => $field) {
            if (!array_key_exists($field, $result['crm_fields'])) {
                $result['crm_fields'][$field] = ' ';
            }
        }
    }

    private static function generateClientData($data, &$result, $isIndividual): void
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
                $result['crm_fields']['UF_CRM_1711519450'] = self::visualFormatNumber($data['act_payment_summ'], true, true);
            }
            if (self::validKeyInArray($data, 'act_payment_goal')) {
                $result['crm_fields']['UF_CRM_1711519550'] = $data['act_payment_goal'];
            }
        }
    }



    private static function visualFormatNumber($number, $with_rubles = false, $stringify = false)
    {
        $words = [];
        $formatted_number = intval(preg_replace("/[^,.0-9]/", '', $number));
        if (!$formatted_number) {
            return $number;
        }

        $words['num'] = $formatted_number;
        $words['text'] = self::num2str($formatted_number);

        if ($with_rubles) {
            $words['ruble'] = self::rubleTermination($formatted_number);
        }
        if ($stringify) {

            $words['text'] = '(' . $words['text'] . ')';

            return implode(' ', $words);
        }

        return $words;
    }

    private static function rubleTermination($num)
    {

        //Оставляем две последние цифры от $num
        $number = substr($num, -2);

        //Если 2 последние цифры входят в диапазон от 11 до 14
        //Тогда подставляем окончание "ЕЙ"
        if ($number > 4 and $number < 21) {
            $term = "ей";
        } else {

            $number = substr($number, -1);

            if ($number == 0) {
                $term = "ей";
            }
            if ($number == 1) {
                $term = "ь";
            }
            if ($number > 1) {
                $term = "я";
            }
        }

        return 'рубл' . $term;
    }

    private static function num2str($num)
    {
        $nul = 'ноль';
        $ten = array(
            array('', 'один', 'два', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'),
            array('', 'одна', 'две', 'три', 'четыре', 'пять', 'шесть', 'семь', 'восемь', 'девять'),
        );
        $a20 = array('десять', 'одиннадцать', 'двенадцать', 'тринадцать', 'четырнадцать', 'пятнадцать', 'шестнадцать', 'семнадцать', 'восемнадцать', 'девятнадцать');
        $tens = array(2 => 'двадцать', 'тридцать', 'сорок', 'пятьдесят', 'шестьдесят', 'семьдесят', 'восемьдесят', 'девяносто');
        $hundred = array('', 'сто', 'двести', 'триста', 'четыреста', 'пятьсот', 'шестьсот', 'семьсот', 'восемьсот', 'девятьсот');
        $unit = array( // Units
            array('', '', '',     1),
            array('', '', '', 0),
            array('тысяча', 'тысячи', 'тысяч', 1),
            array('миллион', 'миллиона', 'миллионов', 0),
            array('миллиард', 'милиарда', 'миллиардов', 0),
        );
        //
        list($rub, $kop) = explode('.', sprintf("%015.2f", floatval($num)));
        $out = array();
        if (intval($rub) > 0) {
            foreach (str_split($rub, 3) as $uk => $v) { // by 3 symbols
                if (!intval($v)) continue;
                $uk = sizeof($unit) - $uk - 1; // unit key
                $gender = $unit[$uk][3];
                list($i1, $i2, $i3) = array_map('intval', str_split($v, 1));
                // mega-logic
                $out[] = $hundred[$i1]; # 1xx-9xx
                if ($i2 > 1) $out[] = $tens[$i2] . ' ' . $ten[$gender][$i3]; # 20-99
                else $out[] = $i2 > 0 ? $a20[$i3] : $ten[$gender][$i3]; # 10-19 | 1-9
                // units without rub & kop
                if ($uk > 1) $out[] = self::morph($v, $unit[$uk][0], $unit[$uk][1], $unit[$uk][2]);
            } //foreach
        } else $out[] = $nul;
        $out[] = self::morph(intval($rub), $unit[1][0], $unit[1][1], $unit[1][2]); // rub
        return trim(preg_replace('/ {2,}/', ' ', join(' ', $out)));
    }

    private static function morph($n, $f1, $f2, $f5)
    {
        $n = abs(intval($n)) % 100;
        if ($n > 10 && $n < 20) return $f5;
        $n = $n % 10;
        if ($n > 1 && $n < 5) return $f2;
        if ($n == 1) return $f1;
        return $f5;
    }


    private static function validKeyInArray(array $data, string $key)
    {
        return array_key_exists($key, $data) && $data[$key] != '';
    }



    private static function visualFormatDeadline($number)
    {
        $words = [];
        $formatted_number = intval(preg_replace("/[^,.0-9]/", '', $number));
        if (!$formatted_number) {
            return $number;
        }

        $words[] = $formatted_number;
        $words[] = '(' .  self::num2str($formatted_number) . ')';
        $words[] = self::deadlineWorkWordTermination($formatted_number);
        $words[] = self::deadlineDayTermination($formatted_number);

        return implode(' ', $words);
    }


    private static function deadlineWorkWordTermination($num)
    {

        //Оставляем две последние цифры от $num
        $number = substr($num, -2);

        //Если 2 последние цифры входят в диапазон от 11 до 14
        //Тогда подставляем окончание "ЕВ"
        if ($number > 4 and $number < 21) {
            $term = "х";
        } else {

            $number = substr($number, -1);

            if ($number == 0) {
                $term = "х";
            }
            if ($number == 1) {
                $term = "й";
            }
            if ($number > 1) {
                $term = "х";
            }
        }

        return 'рабочи' . $term;
    }

    private static function deadlineDayTermination($num)
    {
        //Оставляем две последние цифры от $num
        $number = substr($num, -2);

        //Если 2 последние цифры входят в диапазон от 11 до 14
        //Тогда подставляем окончание "ЕВ"
        if ($number > 4 and $number < 21) {
            $term = "ней";
        } else {

            $number = substr($number, -1);

            if ($number == 0) {
                $term = "ней";
            }
            if ($number == 1) {
                $term = "ень";
            }
            if ($number > 1) {
                $term = "ня";
            }
        }

        return 'д' . $term;
    }
}
