@extends('admin.layouts.departments.sale')

@section('content')
    <h1 class="text-4xl font-semibold mb-6">
        Отчёт по Сотрудникам
    </h1>
    <form action="{{ route('admin.department.sale.user-report') }}" method="GET" class="flex w-6/12 gap-3 mb-6">
        <input type="date" name="date" class="border px-3 py-2" value="{{ $date != null ? $date->format('Y-m-d') : now()->format('Y-m-d') }}">
        <select class="select max-w-md" name="user" id="">
            <option disabled  {{ $user != null ? '' : 'selected' }}  value="">
                Выберите сотрудника
            </option>
            @foreach ($users as $optionUser)
                <option {{ $user == $optionUser ? 'selected' : '' }}  value="{{ $optionUser->id }}">
                    {{ $optionUser->first_name }}  {{ $optionUser->last_name }}
                </option>
            @endforeach   
        </select>

        <button type="submit" class="btn">
            Выбрать
        </button>

    </form>
    @if (isset($report) && !empty($report))
        {{ var_dump($report) }}
    @endif
@endsection
