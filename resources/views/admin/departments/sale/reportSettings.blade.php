@extends('admin.layouts.departments.sale')

@section('content')
    <h1 class="text-4xl font-semibold mb-6">Настройка планов</h1>
    <form action="{{ route('admin.department.sale.report-settings') }}" method="GET" class="flex w-1/2 gap-3 mb-6">
        <input type="month" name="date" class="border px-3 py-1"
            value="{{ $date != null ? $date->format('Y-m') : now()->format('Y-m') }}">
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
    @if ($errors->any())
        <ul class="flex w-fit flex-col gap-1 mb-4">
            @foreach ($errors->all() as $error)
                <li class="text-red-400">{{ $error }}</li>
            @endforeach
        </ul>
    @endif
    <div class="grid grid-cols-3 gap-8">
        @include('admin.departments.sale.settings.mounthPlan')
        <div class="flex flex-col gap-4">
            @include('admin.departments.sale.settings.bonusPlan')
            @include('admin.departments.sale.settings.doublePlan')
            @include('admin.departments.sale.settings.weekPlan')
            @include('admin.departments.sale.settings.superPlan')
            @include('admin.departments.sale.settings.b3Plan')
            @include('admin.departments.sale.settings.b4Plan')
        </div>
        <div class="flex flex-col gap-4">
            @include('admin.departments.sale.settings.bPlan', [
                'title' => 'План Б1',
                'planType' => $workPlanClass::B1_PLAN,
            ])
            @include('admin.departments.sale.settings.bPlan', [
                'title' => 'План Б2',
                'planType' => $workPlanClass::B2_PLAN,
            ])
        </div>
    </div>
    @include('admin.departments.sale.settings.percentLadder')
@endsection
