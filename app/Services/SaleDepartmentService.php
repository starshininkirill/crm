<?php

namespace App\Services;

use DateTime;
use App\Models\User;
use App\Models\Payment;
use App\Helpers\DateHelper;
use App\Models\ServiceCategory;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;

class SaleDepartmentService
{

    public function generateUserReportData(Carbon $date, User $user): array
    {
        $report = [];
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();
        $workingDays = DateHelper::getWorkingDaysInMonth($date);

        $contractIds = $user->contracts->pluck('id')->unique()->toArray();

        $payments = Payment::whereBetween('confirmed_at', [$startOfMonth, $endOfMonth])
            ->whereIn('contract_id', $contractIds)
            ->where('status', 'close')
            ->get()
            ->groupBy('type');

        $newPaymentsGroupedByDate = $this->groupPaymentsByDate($payments->get(Payment::TYPE_NEW, collect()));
        $oldPaymentsGroupedByDate = $this->groupPaymentsByDate($payments->get(Payment::TYPE_OLD, collect()));

        foreach ($workingDays as $day) {
            $dayFormatted = Carbon::parse($day)->format('Y-m-d');
            $dayNewPayments = $newPaymentsGroupedByDate->get($dayFormatted, collect());
            $dayOldPayments = $oldPaymentsGroupedByDate->get($dayFormatted, collect());

            $newPayments = $dayNewPayments->sum('value');
            $oldPayments = $dayOldPayments->sum('value');

            $serviceCounts = $this->calculateServiceCounts($dayNewPayments);

            $report[] = [
                'date' => $day,
                'newMoney' => $newPayments,
                'oldMoney' => $oldPayments,
                'individual_sites' => $serviceCounts['individual_sites'],
                'readies_sites' => $serviceCounts['readies_sites'],
                'rk' => $serviceCounts['rk'],
                'seo' => $serviceCounts['seo'],
                'other' => $serviceCounts['other'],
            ];
        }

        return $report;
    }

    private function groupPaymentsByDate($payments)
    {
        return $payments->groupBy(function ($payment) {
            return Carbon::parse($payment->confirmed_at)->format('Y-m-d');
        });
    }

    private function calculateServiceCounts($dayPayments)
    {
        $counts = [
            'individual_sites' => 0,
            'readies_sites' => 0,
            'rk' => 0,
            'seo' => 0,
            'other' => 0,
        ];

        if ($dayPayments->isEmpty()) {
            return $counts;
        }

        $uniquePayments = $dayPayments->unique('contract_id');

        foreach ($uniquePayments as $payment) {
            $contract = $payment->contract;
            $services = $contract->services;

            if ($services->isEmpty()) {
                continue;
            }

            foreach ($services as $service) {
                switch ($service->category->type) {
                    case ServiceCategory::INDIVIDUAL_SITE:
                        $counts['individual_sites']++;
                        break;
                    case ServiceCategory::READY_SITE:
                        $counts['readies_sites']++;
                        break;
                    case ServiceCategory::RK:
                        $counts['rk']++;
                        break;
                    case ServiceCategory::SEO:
                        $counts['seo']++;
                        break;
                    case ServiceCategory::OTHER:
                        $counts['other']++;
                        break;
                }
            }
        }

        return $counts;
    }
}
