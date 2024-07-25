@extends('base')

@section('main')
    <div class="contract-page border border-top-0 h-100" id="contract-page">
        <div class="contract-submenu border-bottom">
            <a href="" class="border border-bottom-0">
                Все Платежи
            </a>
            <a href="" class="border border-bottom-0">
                Создать Платеж
            </a>
        </div>
        <div class="contract-page-wrapper">
            @yield('content')
        </div>
    </div>
@endsection
