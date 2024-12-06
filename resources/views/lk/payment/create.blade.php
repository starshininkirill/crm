@extends('lk.base')

@section('content')
    <div>
        @csrf
        <h1 class=" text-4xl font-bold mb-5">
            Создание платежа
        </h1>
        @if (session('success'))
            <div class="mb-3 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
        @csrf
        @if ($errors->any())
            <ul class="flex flex-col gap-1 mb-4">
                @foreach ($errors->all() as $error)
                    <li class="text-red-400">{{ $error }}</li>
                @endforeach
            </ul>
        @endif
        <vue-payment-create-form action="{{ route('contract.store') }}" token="{{ csrf_token() }}" />
    </div>
@endsection
