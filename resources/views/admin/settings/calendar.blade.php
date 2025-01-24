@extends('admin.layouts.settings')

@section('content')
    <h1 class="text-4xl font-semibold mb-6">Календарь рабочих дней</h1>
    <form action="{{ route('admin.settings.calendar') }}" method="GET" class="flex w-1/2 gap-3 mb-6">
        <input type="month" name="date" class="border px-3 py-1"
            value="{{ $date != null ? $date->format('Y-m') : now()->format('Y-m') }}">
        <button type="submit" class="btn">
            Выбрать
        </button>
    </form>
    <div class="grid grid-cols-4 auto-rows-max gap-6">
        @foreach ($months as $month)
            <vue-calendar-month 
                :month-name="{{ json_encode($month['name']) }}" 
                :weeks="{{ json_encode($month['weeks']) }}">
            </vue-calendar-month>
        @endforeach
    </div>
@endsection
 