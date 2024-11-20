<?php

namespace App\Classes;

use App\Models\Client;

class Bitrix
{
    public static function generateDealDocument(array $data)
    {
        $bitrixData = self::prepareDealData($data);
        dd($bitrixData);
    }

    private static function prepareDealData(array $data): array
    {

        $result = [];

        // Временные данные
        $isIndividual = false;

        if (self::validKeyInArray($data, 'client_type') && $data['client_type'] == Client::TYPE_INDIVIDUAL) {
            $isIndividual = true;
        }

        $services = $data['services'];

        if (self::validKeyInArray($data, 'leed')) {
            $result['lead_id'] = $data['leed'];
        }

        if (self::validKeyInArray($data, 'contact_phone')) {
            $result['client_phone'] = $data['contact_phone'];
        }


        // Номер Договора
        if (self::validKeyInArray($data, 'number')) {
            $result['crm_fields']['UF_CRM_1671028945'] = $data['number'];
        }

        // ФИО представителя и номер
        if (self::validKeyInArray($data, 'contact_fio') && self::validKeyInArray($data, 'contact_phone')) {
            $result['crm_fields']['UF_CRM_1685186412058'] = $data['contact_fio'] . ' ' . $data['contact_phone'];
        }


        return $result;
    }

    private static function validKeyInArray(array $data, string $key)
    {
        return array_key_exists($key, $data) && $data[$key] != '';
    }
}
