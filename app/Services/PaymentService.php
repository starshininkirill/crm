<?php

namespace App\Services;

use App\Exceptions\Business\BusinessException;
use App\Helpers\TextFormaterHelper;
use App\Models\Contract;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class PaymentService
{

    public function update(Payment $payment, array|Collection $data): bool
    {
        return $payment->update($data);
    }

    public function getShortlist(Payment $payment): array
    {
        $payments = collect();

        $contract = Contract::query()
            ->whereHas('client', function ($query) use ($payment) {
                $query->where('inn', $payment->inn);
            })
            ->first();

        if (!$contract) return [];

        $payments = $contract->childs
            ->prepend($contract)
            ->flatMap(fn($c) => $c->payments()->where('status', Payment::STATUS_WAIT)->get());

        if ($payments->isEmpty()) return [];

        return $payments->map(function ($payment) {
            $contract = $payment->contract;
            return [
                'id' => $payment->id,
                'value' => TextFormaterHelper::getPrice($payment->value),
                'contract' => $contract,
                'order' => $payment->order,
                'inn' => $contract->parent?->client->inn ?? $contract->client->inn
            ];
        })->toArray();
    }

    public function attachPayment(Payment $newPayment, Payment $oldPayment, User $user): void
    {
        DB::beginTransaction();

        try {
            $oldPayment->value = $newPayment->value;
            $oldPayment->inn = $newPayment->inn;
            $oldPayment->status = Payment::STATUS_CLOSE;
            $oldPayment->organization_id = $newPayment->organization_id;
            $oldPayment->confirmed_at = Carbon::now();
            $oldPayment->responsible_id = $user->id;
            $oldPayment->operation_id = $newPayment->operation_id ?? null;
            $oldPayment->type = $oldPayment->determineType($newPayment);
            $oldPayment->create_at = Carbon::now();

            $newPayment->delete();

            $oldPayment->save();

            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();

            throw new BusinessException('Не удалось прикрепить платёж');
        }
    }
}
