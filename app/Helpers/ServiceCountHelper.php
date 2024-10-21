<?php

namespace App\Helpers;

use App\Models\ServiceCategory;
use Illuminate\Support\Collection;

class ServiceCountHelper
{
    public static function calculateServiceCountsByContracts(Collection $contracts): Collection
    {
        $counts = [
            ServiceCategory::INDIVIDUAL_SITE => 0,
            ServiceCategory::READY_SITE => 0,
            ServiceCategory::RK => 0,
            ServiceCategory::SEO => 0,
            ServiceCategory::OTHER => 0,
        ];

        foreach ($contracts as $contract) {
            foreach ($contract->services as $service) {
                if ($service->category) {
                    self::incrementServiceCount($counts, $service->category->type);
                }
            }
        }

        return collect($counts);
    }

    public static function calculateServiceCountsByPayments(Collection $payments): array
    {
        $counts = [
            ServiceCategory::INDIVIDUAL_SITE => 0,
            ServiceCategory::READY_SITE => 0,
            ServiceCategory::RK => 0,
            ServiceCategory::SEO => 0,
            ServiceCategory::OTHER => 0,
        ];

        foreach ($payments->unique('contract_id') as $payment) {
            foreach ($payment->contract->services as $service) {
                self::incrementServiceCount($counts, $service->category->type);
            }
        }

        return $counts;
    }

    private static function incrementServiceCount(array &$counts, string $serviceType): void
    {
        switch ($serviceType) {
            case ServiceCategory::INDIVIDUAL_SITE:
                $counts[ServiceCategory::INDIVIDUAL_SITE]++;
                break;
            case ServiceCategory::READY_SITE:
                $counts[ServiceCategory::READY_SITE]++;
                break;
            case ServiceCategory::RK:
                $counts[ServiceCategory::RK]++;
                break;
            case ServiceCategory::SEO:
                $counts[ServiceCategory::SEO]++;
                break;
            case ServiceCategory::OTHER:
                $counts[ServiceCategory::OTHER]++;
                break;
        }
    }
}
