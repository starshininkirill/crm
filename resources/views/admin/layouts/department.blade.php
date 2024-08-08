@extends('admin')

@section('main')
    <div class="grow w-full py-8 px-2" id="contract-page">
        <div class="flex gap-3 mb-4 border-b">
            <x-admin-subnav-link route="admin.department.index">
                Все Отделы
            </x-admin-subnav-link>
            <x-admin-subnav-link route="admin.department.create">
                Создать отдел
            </x-admin-subnav-link>
            <x-admin-subnav-link route="admin.department.position.create">
                Создать Должность
            </x-admin-subnav-link>
        </div>
        <div class="contract-page-wrapper flex flex-col">
            @yield('content')
        </div>
    </div>
@endsection
