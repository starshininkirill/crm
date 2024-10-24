@extends('admin')

@section('main')
    <div class="grow w-full py-8 px-2" id="departments-sale-page">
        <div class="flex gap-3 mb-4 border-b">
            <x-admin-subnav-link route="admin.department.sale.index">
                Главная
            </x-admin-subnav-link>
            <x-admin-subnav-link route="admin.department.sale.user-report">
                Отчёт по менеджерам
            </x-admin-subnav-link>
            <x-admin-subnav-link route="admin.department.sale.report-settings">
                Настройка планов
            </x-admin-subnav-link>
        </div>
        <div class="contract-page-wrapper flex flex-col">
            @yield('content')
        </div>
    </div>
@endsection
