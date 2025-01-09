@extends('admin')

@section('main')
    <div class="grow w-full py-8 px-2">
        <div class="flex gap-3 mb-4 border-b">
            <x-admin-subnav-link route="admin.organization.index">
                Все организации
            </x-admin-subnav-link>
            <x-admin-subnav-link route="admin.organization.create">
                Создать организацию
            </x-admin-subnav-link>
        </div>
        <div class="flex flex-col">
            @yield('content')
        </div>
    </div>
@endsection
