@extends('admin')

@section('main')
    <div class="grow w-full py-8 px-2" id="contract-page">
        <div class="flex gap-3 mb-4 border-b">
            <x-admin-subnav-link route="admin.contract.index">
                Все Договоры
            </x-admin-subnav-link>
        </div>
        <div class="contract-page-wrapper flex flex-col">
            @yield('content')
        </div>
    </div>
@endsection
