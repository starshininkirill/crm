@extends('admin.layouts.settings')

@section('content')
    <h1 class="text-4xl font-semibold mb-6">Настройка рабочих недель</h1>
    <form action="{{ route('admin.settings.finance-week') }}" method="GET" class="flex w-1/2 gap-3 mb-6">
        <input type="month" name="date" class="border px-3 py-1"
            value="{{ $date != null ? $date->format('Y-m') : now()->format('Y-m') }}">
        <button type="submit" class="btn">
            Выбрать
        </button>
    </form>

    <form class="flex max-w-md flex-col gap-4" action="">
        <div class="flex justify-between gap-4">
            <input class="border px-3 py-1" type="date" name="week[]['date_start']" value="{{ $date != null ? $date->format('Y-m-d') : now()->format('Y-m-d') }}">
            <input class="border px-3 py-1" type="date" name="week[]['date_end']" value="{{ $date != null ? $date->format('Y-m-d') : now()->format('Y-m-d') }}">
            <input class="border px-3 py-1" type="number" name="weeknum" disabled value="1">
        </div>
        <div class="flex justify-between gap-4">
            <input class="border px-3 py-1" type="date" name="week[]['date_start']" value="{{ $date != null ? $date->format('Y-m-d') : now()->format('Y-m-d') }}">
            <input class="border px-3 py-1" type="date" name="week[]['date_end']" value="{{ $date != null ? $date->format('Y-m-d') : now()->format('Y-m-d') }}">
            <input class="border px-3 py-1" type="number" name="weeknum" disabled value="1">
        </div>
    </form>
@endsection
