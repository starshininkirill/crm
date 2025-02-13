@extends('admin')

@section('main')
    <div class="grow w-full py-8 px-2" id="departments-sale-page">
        <div class="flex gap-3 mb-4 border-b">
            <x-admin-subnav-link route="admin.sale-department.index">
                Главная
            </x-admin-subnav-link>
            <x-admin-subnav-link route="admin.sale-department.user-report">
                Отчёт по менеджерам
            </x-admin-subnav-link>
            <x-admin-subnav-link route="admin.sale-department.report-settings">
                Настройка планов
            </x-admin-subnav-link>
        </div>
        <div class="contract-page-wrapper flex flex-col">
            @yield('content')
        </div>
    </div>
@endsection
