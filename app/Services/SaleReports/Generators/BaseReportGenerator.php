<?php

namespace App\Services\SaleReports\Generators;

use App\Helpers\DateHelper;
use App\Models\Finance\Payment;
use App\Services\SaleReports\Builders\ReportDTOBuilder;
use App\Services\SaleReports\DTO\ReportDTO;
use App\Services\UserServices\UserService;
use Carbon\Carbon;
use Illuminate\Support\Collection;

abstract class BaseReportGenerator
{
    public function __construct(
        protected ReportDTOBuilder $reportDTOBuilder,
        protected UserService $userService,
    ) {}

    protected function unusedPayments(ReportDTO $fullData)
    {
        $unusedPayments = $fullData->payments->diff($fullData->usedPayments);

        $newMoney = $unusedPayments->where('type', Payment::TYPE_NEW)->sum('value');
        $oldMoney = $unusedPayments->where('type', Payment::TYPE_OLD)->sum('value');
        $allMoney = $newMoney + $oldMoney;

        return collect([
            'newMoney' => $newMoney,
            'oldMoney' => $oldMoney,
            'allMoney' => $allMoney,
        ]);
    }

    protected function groupPaymentsByDate(ReportDTO $reportData, Collection $payments): Collection
    {
        $workingDays = $reportData->workingDays;
        return $payments->groupBy(function ($payment) use ($workingDays, $reportData) {
            $paymentDate = Carbon::parse($payment->created_at);

            return $workingDays->contains($paymentDate)
                ? $paymentDate
                : DateHelper::getNearestPreviousWorkingDay($paymentDate, $reportData->workingDays);
        });
    }
}
