@extends('lk.base')

@section('content')
    <div>
        <form action="{{ route('lk.payment.store') }}" method="POST" class=" max-w-md">
            @csrf
            <h1 class=" text-4xl font-bold mb-5">
                Создание платежа
            </h1>
            @if (session('success'))
                <div class="mb-3 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                    role="alert">
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
            <div class="flex flex-col gap-3">
                <x-form-input type="text" name="contract_id" placeholder="Номер договора*" label="Номер договора*" />
                <x-form-input type="number" name="value" placeholder="Сумма платежа*" label="Введите сумму" />
                <x-form-input type="number" name="order" placeholder="Номер платежа" label="Номер платежа" />
                <label for="technical" class="flex flex-row justify-start gap-2 text-md font-semibold cursor-pointer">
                    <input id="technical" name="is_technical" type="checkbox" value="1" class="">
                    Технический платёж
                </label>
                @if ($paymentMethods->isNotEmpty())
                    <div class="type-group">
                        <span class="font-semibold mb-2 inline-block">Метод оплаты</span>
                        <div class=" grid grid-cols-3 gap-2">
                            @foreach ($paymentMethods as $method)
                                <label for="method-{{ $method->id }}" class=" cursor-pointer">
                                    <input value="{{ $method->id }}" id="method-{{ $method->id }}" name="payment_method_id"
                                        type="radio">
                                    {{ $method->name }}
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endif
                <span class="text-2xl font-bold">ТУТ БУДЕТ ЧЕК</span>
                <button class="btn" type="submit">
                    Создать
                </button>
            </div>
        </form>
    </div>
@endsection
