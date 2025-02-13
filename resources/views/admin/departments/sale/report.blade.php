@extends('admin.layouts.departments.sale')

@section('content')
    <h1 class="text-4xl font-semibold mb-6">
        Отчёт по Сотрудникам
    </h1> 
    {{-- <vue-user-sale-report-form
        action="{{ route('admin.sale-department.user-report') }}"
        :departments="{{ json_encode($departments) }}"
        :users="{{ json_encode($selectUsers) }}"
        :initial-department="{{ json_encode($selectedDepartment) }}"
        :initial-user="{{ json_encode($user) }}"
        initial-date="{{ $date != null ? $date->format('Y-m') : now()->format('Y-m') }}"
    ></vue-user-sale-report-form> --}}
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
        {{-- <div class="text-2xl font-semibold">
            {{ $optionUser->first_name }} {{ $optionUser->last_name }}
        </div> --}}


        <table class="reports w-1/2">
            @if (!$daylyReport->isEmpty())
                @include('admin.departments.sale.tables.DailyReport', [
                    'daylyReport' => $daylyReport,
                ])
            @endif
            @if (!$motivationReport->isEmpty())
                @include('admin.departments.sale.tables.weeksReport', [
                    'weeks' => $motivationReport,
                ])
                @include('admin.departments.sale.tables.userMotivationReport')
            @endif
        </table>



        <table class="pivot-reports w-1/2 h-fit">
            @if (!$pivotDaily->isEmpty())
                @include('admin.departments.sale.tables.DailyReport', [
                    'daylyReport' => $pivotDaily,
                ])
            @endif
            @if (!$pivotWeeks->isEmpty())
                @include('admin.departments.sale.tables.weeksReport', [
                    'weeks' => $pivotWeeks,
                ])
            @endif
            @if (!$generalPlan->isEmpty())
                @include('admin.departments.sale.tables.generalPlan')
            @endif
        </table>
    </div>
    @if (!$pivotUsers->isEmpty())
        <div class="w-100 mt-6">
            @include('admin.departments.sale.tables.pivotUsers')
        </div>
    @endif
@endsection
