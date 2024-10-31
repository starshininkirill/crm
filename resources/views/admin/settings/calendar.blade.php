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
            <div class="bg-gray-800 rounded shadow h-fit border border-gray-200">
                <table class="w-full text-center text-white border-collapse">
                    <thead>
                        <tr>
                            <th colspan="7" class="py-2 font-semibold bg-gray-800 border-b border-gray-600">
                                {{ $month['name'] }}
                            </th>
                        </tr>
                        <tr class="bg-gray-800 font-semibold border-white border-t-2 border-b-2">
                            <th class="w-12 h-12 border border-gray-200">Пн</th>
                            <th class="w-12 h-12 border border-gray-200">Вт</th>
                            <th class="w-12 h-12 border border-gray-200">Ср</th>
                            <th class="w-12 h-12 border border-gray-200">Чт</th>
                            <th class="w-12 h-12 border border-gray-200">Пт</th>
                            <th class="w-12 h-12 border border-gray-200">Сб</th>
                            <th class="w-12 h-12 border border-gray-200">Вс</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($month['weeks'] as $week)
                            <tr>
                                @foreach ($week as $day)
                                    @if ($day)
                                        <livewire:calendar-day :$day>
                                        @else
                                            <td class=" bg-white w-12 h-12 border border-gray-200"></td>
                                    @endif
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endforeach
    </div>
@endsection
