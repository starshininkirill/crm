@extends('layouts.contract')

@section('content')
    <h1>Договора</h1>
    <div class="contracts">
        @if ($contracts->isEmpty())
            <h2>Договоров не найдено</h2>
        @else
            @foreach($contracts as $contract)
            <a href="{{ route('contract.show', $contract->id) }}" class="contract border-bottom">
                <div class="contract__name">
                    {{ $contract->client }}
                </div>
            </a>
            @endforeach
        @endif
    </div>
@endsection
