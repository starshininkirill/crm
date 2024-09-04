<?php

namespace App\Services;

use App\Models\User;
use App\Models\Payment;
use App\Helpers\DateHelper;
use App\Models\ServiceCategory;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class SaleDepartmentService
{
    public function generateUserReportData(Carbon $date, User $user): Collection
    {
        $report = collect();
        $workingDays = DateHelper::getWorkingDaysInMonth($date);
        $payments = $this->getPaymentsForUser($date, $user);

        $newPaymentsGroupedByDate = $this->groupPaymentsByDate($payments[Payment::TYPE_NEW] ?? collect(), $workingDays);
        $uniqueNewPaymentsGroupedByDate = $this->groupPaymentsByDate($payments[Payment::TYPE_NEW]->unique('contract_id') ?? collect(), $workingDays);
        $oldPaymentsGroupedByDate = $this->groupPaymentsByDate($payments[Payment::TYPE_OLD] ?? collect(), $workingDays);

        foreach ($workingDays as $day) {
            $dayFormatted = Carbon::parse($day)->format('Y-m-d');
            $report[] = $this->generateDailyReport($dayFormatted, $newPaymentsGroupedByDate, $oldPaymentsGroupedByDate, $uniqueNewPaymentsGroupedByDate);
        }

        return $report;
    }

    private function getPaymentsForUser(Carbon $date, User $user): Collection
    {
        $startOfMonth = $date->copy()->startOfMonth();
        $endOfMonth = $date->copy()->endOfMonth();
        $contractIds = $user->contracts->pluck('id')->unique();

        return Payment::whereBetween('created_at', [$startOfMonth, $endOfMonth])
            ->whereIn('contract_id', $contractIds)
            ->where('status', 'close')
            ->get()
            ->groupBy('type');
    }

    private function groupPaymentsByDate(Collection $payments, array $workingDays): Collection
    {
        return $payments->groupBy(function ($payment) use ($workingDays) {
            $paymentDate = Carbon::parse($payment->created_at)->format('Y-m-d');

            return in_array($paymentDate, $workingDays) 
                ? $paymentDate 
                : $this->getNearestPreviousWorkingDay($paymentDate, $workingDays);
        });
    }

    private function getNearestPreviousWorkingDay(string $date, array $workingDays): string
    {
        $date = Carbon::parse($date);

        while (!in_array($date->format('Y-m-d'), $workingDays)) {
            $date->subDay();
        }

        return $date->format('Y-m-d');
    }

    private function generateDailyReport(string $day, Collection $newPaymentsGroupedByDate, Collection $oldPaymentsGroupedByDate, Collection $uniqueNewPaymentsGroupedByDate): array
    {
        $dayNewPayments = $newPaymentsGroupedByDate->get($day, collect());
        $dayOldPayments = $oldPaymentsGroupedByDate->get($day, collect());
        $uniqueDayNewPayments = $uniqueNewPaymentsGroupedByDate->get($day, collect());

        $newPaymentsSum = $dayNewPayments->sum('value');
        $oldPaymentsSum = $dayOldPayments->sum('value');
        $serviceCounts = $this->calculateServiceCounts($uniqueDayNewPayments);

        return [
            'date' => $day,
            'newMoney' => $newPaymentsSum,
            'oldMoney' => $oldPaymentsSum,
            'individual_sites' => $serviceCounts['individual_sites'],
            'readies_sites' => $serviceCounts['readies_sites'],
            'rk' => $serviceCounts['rk'],
            'seo' => $serviceCounts['seo'],
            'other' => $serviceCounts['other'],
        ];
    }

    private function calculateServiceCounts(Collection $dayPayments): array
    {
        $counts = [
            'individual_sites' => 0,
            'readies_sites' => 0,
            'rk' => 0,
            'seo' => 0,
            'other' => 0,
        ];

        foreach ($dayPayments->unique('contract_id') as $payment) {
            foreach ($payment->contract->services as $service) {
                $this->incrementServiceCount($counts, $service->category->type);
            }
        }

        return $counts;
    }

    private function incrementServiceCount(array &$counts, string $serviceType): void
    {
        switch ($serviceType) {
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