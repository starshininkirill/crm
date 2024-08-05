@extends('admin')

@section('main')
    <div class="grow w-full py-8 px-2" id="contract-page">
        <div class="flex gap-3 mb-4 border-b">
            <a href="{{ route('admin.contract.index') }}" class="px-4 py-2 border border-b-0 ">
                Все Договора
            </a>
            <a href="{{ route('admin.contract.create') }}" class="px-4 py-2 border border-b-0">
                Создать договор
            </a>
        </div>
        <div class="contract-page-wrapper flex flex-col">
            @yield('content')
        </div>
    </div>
@endsection
