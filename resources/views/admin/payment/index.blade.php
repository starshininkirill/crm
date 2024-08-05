@extends('admin.layouts.payment')

@section('content')
    <h1>Платежи</h1>
    <div class="payments">
        @if ($payments->isEmpty())
            <h2>Платежей не найдено</h2>
        @else
            @foreach($payments as $payment)
            <a href="{{ route('admin.payment.show', $payment->id) }}" class="payment border-bottom">
                <div class="payment__name">
                    Платеж №: {{ $payment->id }}
                </div>
            </a>
            @endforeach
        @endif
    </div>
@endsection
