@extends('admin')

@section('main')
    <div class="grow w-full py-8 px-2" id="contract-page">
        <div class="flex gap-3 mb-4 border-b">
            <x-admin-subnav-link route="admin.service.category.index">
                Категории услуг
            </x-admin-subnav-link>
            <x-admin-subnav-link route="admin.service.category.create">
                Создать Категорию услуг
            </x-admin-subnav-link>
            <x-admin-subnav-link route="admin.service.index">
                Все Услуги
            </x-admin-subnav-link>
            <x-admin-subnav-link route="admin.service.create">
                Создать услугу
            </x-admin-subnav-link>
        </div>
        <div class="contract-page-wrapper flex flex-col">
            @yield('content')
        </div>
    </div>
@endsection
