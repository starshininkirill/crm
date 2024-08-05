@extends('admin.layouts.payment')

@section('content')
    <h1>Платеж №: {{ $payment->id }}</h1>
    <div class="payment-info">
        <div class="value">
            Сумма: {{ $payment->value }}
        </div>
        <div class="status">
            Статус: {{ $payment->status }}
        </div>
        <div class="date pb-3">
            Создан: {{ $payment->created_at->format('d.m.Y') }}
        </div>
        <a href="{{ route('contract.show', $payment->contract->id) }}" class="contract">
            Сделка: {{ $payment->contract->client }}
        </a>
    </div>
@endsection
