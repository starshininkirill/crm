@extends('layouts.contract')

@section('content')
    <h1>Договора</h1>
    <div class="contracts">
        @if ($contracts->isEmpty())
            <h2>Договоров не найдено</h2>
        @else
            @foreach($contracts as $contract)
            <div class="contract">
                <div class="contract__name">
                    {{ $contract->client }}
                </div>
                <div class="contract__amount-summ">
                    {{ $contract->amount_price }}
                </div>
            </div>
            @endforeach
        @endif
    </div>
@endsection
