@extends('layouts.contract')

@section('content')
    <h1 class="pb-4">Договор: {{ $contract->client }}</h1>
    <h4 class="pb-1">Цена: {{ $contract->amount_price }} Р</h4>
    <h4 class="border-bottom pb-3">Услуга: {{ $contract->service->name }}</h4>

    @if (!empty($contract->payments))
        <h3>Платежи</h3>
        <div class="payments d-flex flex-column gap-1">
            @foreach ($contract->payments as $payment)
                <a href="{{ route('payment.show', $payment->id) }}" class="payment">
                    {{ $payment->order }}й платеж: {{ $payment->value }}
                </a>
            @endforeach
        </div>
    @endif
@endsection
