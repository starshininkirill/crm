@extends('admin.layouts.contract')

@section('content')
    <h1 class="text-4xl font-semibold mb-6">Договор</h1>
    <form action="{{ route('admin.contract.store') }}" method="POST" class="max-w-md">
        @if (session('success'))
            <div class="mb-3 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
        @csrf

        @csrf
        @if ($errors->any())
            <ul class="flex flex-col gap-1 mb-4">
                @foreach ($errors->all() as $error)
                    <li class="text-red-400">{{ $error }}</li>
                @endforeach
            </ul>
        @endif
        <div class="flex flex-col gap-2">

            <div class="font-semibold text-xl my-2">
                Информация о договоре
            </div>


            <x-form-input type="number" name="number" placeholder="Номер договора" label="Номер договора" />

            <div class="block text-xl font-medium leading-6 text-gray-900 mb-2">
                Услуги
            </div>

            @if ($services->isNotEmpty())
                <div class="grid grid-cols-3">
                    @foreach ($services as $service)
                        <label for="service-{{ $service->id }}" class=" cursor-pointer">
                            <input type="checkbox" id="service-{{ $service->id }}" name="service[]"
                                value="{{ $service->id }}">
                            <span>{{ $service->name }}</span>
                        </label>
                    @endforeach
                </div>
            @endif

            <x-form-input type="number" name="amount_price" placeholder="Общая стоимость" label="Общая стоимость" />
            <div class="payments-wrapper flex flex-col gap-2">
                <x-form-input type="number" name="payments[]" placeholder="Введите сумму" label="Платеж 1" />
            </div>
            <button type="button" id="add-payment" class="btn">Добавить платеж</button>

            <x-form-input type="text" name="comment" placeholder="Комментарий" label="Комментарий" />

            <div class="font-semibold text-xl my-2">
                Информация о клиенте
            </div>

            <x-form-input type="text" name="client_name" placeholder="Имя клиента" label="Имя клиента" />
            <x-form-input type="text" name="client_city" placeholder="Город клиента" label="Город клиента" />
            <x-form-input type="email" name="client_email" placeholder="email клиента" label="email клиента" />
            <x-form-input type="phone" name="client_phone" placeholder="Телефон клиента" label="Телефон клиента" />
            <x-form-input type="text" name="client_company" placeholder="Название компании" label="Название компании" />
            <x-form-input type="text" name="client_inn" placeholder="Инн клиента" label="Инн клиента" />

            <button class="btn" type="submit">
                Создать
            </button>
        </div>
    </form>
@endsection
