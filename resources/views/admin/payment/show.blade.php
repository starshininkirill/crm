@extends('admin.layouts.payment')

@section('content')
    <div class="bg-white p-6 rounded-lg shadow-md w-1/3">
        <h1 class="text-3xl font-semibold mb-4">Платеж №: {{ $payment->id }}</h1>

        <div class="payment-info text-lg">
            <div class="contract font-semibold mb-3">
                Сделка(Направление):
                @if ($payment->contract)
                    <a class="text-blue-500 hover:underline"
                        href="{{ route('admin.contract.show', $payment->contract->id) }}">{{ $payment->contract->number }}</a>
                @else
                    Не прикреплён
                @endif
            </div>
            <div class="value mb-3">
                <span class="font-semibold">Сумма:</span> {{ number_format($payment->value, 0, ' ', ' ') }} руб.
            </div>
            <div class="status mb-3">
                <span class="font-semibold">Статус:</span>
                <span class="{{ $payment->status === 'close' ? 'text-green-600' : 'text-red-600' }}">
                    {{ $payment->getStatusNameAttribute() }}
                </span>
            </div>
            <div class="status mb-3">
                <span class="font-semibold">Тип:</span>
                <span>
                    {{ $payment->formatedType() != '' ? $payment->formatedType() : 'Не определён' }}
                </span>
            </div>
            <div class="date mb-3">
                <span class="font-semibold">Метод оплаты:</span>
                {{ $payment->generetePaymentMethodHierarchy() != '' ? $payment->generetePaymentMethodHierarchy() : 'Не определён' }}
            </div>
            <div class="date mb-3">
                <span class="font-semibold">Технический платёж:</span> {{ $payment->is_technical ? 'Да' : 'Нет' }}
            </div>
            <div class="date mb-3">
                <span class="font-semibold">Подтвержён:</span>
                {{ $payment->confirmed_at != null ? $payment->confirmed_at->format('d.m.Y H:i') : 'Не подтвержён' }}
            </div>
            <div class="date mb-3">
                <span class="font-semibold">Создан:</span> {{ $payment->created_at->format('d.m.Y H:i') }}
            </div>
            <div class="date">
                <span class="font-semibold">Ответственный:</span>
                @if ($payment->responsible)
                    <a class="text-blue-500 hover:underline"
                        href="{{ route('admin.user.show', $payment->responsible->id) }}">
                        {{ $payment->responsible->first_name . ' ' . $payment->responsible->last_name }}
                    </a>
                @else
                    Не прикреплён
                @endif
            </div>
        </div>
    </div>
@endsection
