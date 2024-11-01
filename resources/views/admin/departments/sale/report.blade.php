@extends('admin.layouts.departments.sale')

@section('content')
    <h1 class="text-4xl font-semibold mb-6">
        Отчёт по Сотрудникам
    </h1>
    <form action="{{ route('admin.department.sale.user-report') }}" method="GET" class="flex w-1/2 gap-3 mb-6">
        <input type="month" name="date" class="border px-3 py-1"
            value="{{ $date != null ? $date->format('Y-m') : now()->format('Y-m') }}">
        <select class="select max-w-md" name="user" id="">
            <option disabled {{ $user != null ? '' : 'selected' }} value="">
                Выберите сотрудника
            </option>
            @foreach ($selectUsers as $optionUser)
                <option {{ optional($user)->id == $optionUser->id ? 'selected' : '' }} value="{{ $optionUser->id }}">
                    {{ $optionUser->first_name }} {{ $optionUser->last_name }}
                </option>
            @endforeach
        </select>

        <button type="submit" class="btn">
            Выбрать
        </button>
    </form>
    @if ($daylyReport->isEmpty() && $motivationReport->isEmpty())
        @if (!$error)
            <h2>Данные для отчёта не найдены</h2>
        @endif
    @endif
    @if ($error)
        <div class="mb-2 font-semibold">
            {{ $error }}
        </div>
    @endif
    <div class="flex gap-4">
        <div class="reports flex flex-col gap-6 w-1/2">
            @if (!$daylyReport->isEmpty())
                @include('admin.departments.sale.tables.userDailyReport')
            @endif
            @if (!$motivationReport->isEmpty())
                @include('admin.departments.sale.tables.userMotivationReport')
            @endif
        </div>
        <div class="pivot-reports flex flex-col gap-5 w-7/12">
            <div class="w-full flex flex-col gap-5">
                @if (!$pivotWeeks->isEmpty())
                    @include('admin.departments.sale.tables.pivotWeeks')
                @endif
                @if (!$generalPlan->isEmpty())
                    @include('admin.departments.sale.tables.generalPlan')
                @endif
            </div>
            @if (!$pivotDaily->isEmpty())
                @include('admin.departments.sale.tables.pivotDaily')
            @endif
        </div>
    </div>
    @if (!$pivotUsers->isEmpty())
        <div class="w-100 mt-6">
            @include('admin.departments.sale.tables.pivotUsers')
        </div>
    @endif
@endsection
