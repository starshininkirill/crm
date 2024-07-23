@extends('base')

@section('main')
    <div class="contract-page border border-top-0 h-100" id="contract-page">
        <div class="contract-submenu border-bottom">
            <a href="{{ route('contract.index') }}" class="border border-bottom-0">
                Все Договора
            </a>
            <a href="{{ route('contract.create') }}" class="border border-bottom-0">
                Создать договор
            </a>
        </div>
        <div class="contract-page-wrapper">
            @yield('content')
        </div>
    </div>
@endsection
