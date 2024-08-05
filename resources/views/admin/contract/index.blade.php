@extends('admin.layouts.contract')

@section('content')
    <h1 class="text-4xl font-semibold mb-6">Договора</h1>
    <div class="contracts">
        @if ($contracts->isEmpty())
            <h2>Договоров не найдено</h2>
        @else
            @foreach($contracts as $contract)
            <a href="{{ route('admin.contract.show', $contract->id) }}" class="contract border-bottom">
                <div class="contract__name">
                    {{ $contract->client }}
                </div>
            </a>
            @endforeach
        @endif
    </div>
@endsection
