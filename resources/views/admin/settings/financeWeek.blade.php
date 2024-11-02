@extends('admin.layouts.settings')

@section('content')
    <h1 class="text-4xl font-semibold mb-6">Настройка рабочих недель</h1>
    <form action="{{ route('admin.settings.finance-week') }}" method="GET" class="flex w-1/2 gap-3 mb-6">
        <input type="month" name="date" class="border px-3 py-1" value="{{ $date->format('Y-m') }}">
        <button type="submit" class="btn">
            Выбрать
        </button>
    </form>

    @if (session('success'))
        <div class="mb-3 w-fit bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
            role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    @endif

    @csrf
    @if ($errors->any())
        <ul class="flex flex-col gap-1 mb-4">
            @foreach ($errors->all() as $error)
                <li class="text-red-400">{{ $error }}</li>
            @endforeach
        </ul>
    @endif

    <form class="flex max-w-md flex-col gap-4" method="POST" action="{{ route('finance-week.set-weeks') }}">
        @csrf
        @if (!$financeWeeks->isEmpty())
            @method('PUT')
        @endif
        @php
            $startOfMonth = $date->startOfMonth()->format('Y-m-d');
            $endOfMonth = $date->endOfMonth()->format('Y-m-d');
        @endphp
        @for ($i = 1; $i < 6; $i++)
            @if ($financeWeeks->where('weeknum', $i)->first() != null)
                @php
                    $week = $financeWeeks->where('weeknum', $i)->first();
                @endphp
                <div class="flex justify-between gap-4">
                    <label class="flex flex-col font-semibold gap-2">
                        Начало недели
                        <input min="{{ $startOfMonth }}" max="{{ $endOfMonth }}" class="border px-3 py-1" type="date"
                            name="week[{{ $i }}][date_start]" value="{{ $week['date_start'] }}">
                    </label>
                    <label class="flex flex-col font-semibold gap-2">
                        Конец недели
                        <input min="{{ $startOfMonth }}" max="{{ $endOfMonth }}" class="border px-3 py-1" type="date"
                            name="week[{{ $i }}][date_end]" value="{{ $week['date_end'] }}">
                    </label>
                    <label class="flex flex-col font-semibold gap-2">
                        Номер недели
                        <input class="border px-3 py-1" type="number" name="week[{{ $i }}][weeknum]" readonly
                            value="{{ $i }}">
                    </label>
                </div>
            @else
                <div class="flex justify-between gap-4">
                    <label class="flex flex-col font-semibold gap-2">
                        Начало недели
                        <input min="{{ $startOfMonth }}" max="{{ $endOfMonth }}" class="border px-3 py-1" type="date"
                            name="week[{{ $i }}][date_start]" value="{{ $date }}">
                    </label>
                    <label class="flex flex-col font-semibold gap-2">
                        Конец недели
                        <input min="{{ $startOfMonth }}" max="{{ $endOfMonth }}" class="border px-3 py-1" type="date"
                            name="week[{{ $i }}][date_end]" value="{{ $date }}">
                    </label>
                    <label class="flex flex-col font-semibold gap-2">
                        Номер недели
                        <input class="border px-3 py-1" type="number" name="week[{{ $i }}][weeknum]" readonly
                            value="{{ $i }}">
                    </label>
                </div>
            @endif
        @endfor
        <button class="btn">
            Отправить
        </button>
    </form>
@endsection
