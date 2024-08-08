@extends('admin.layouts.contract')

@section('content')
    <h1 class="text-4xl font-semibold mb-6">Договор</h1>
    <form action="{{ route('admin.contract.store') }}" method="POST" class="max-w-md">
        @csrf
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
        <div class="flex flex-col gap-2">
            <x-form-input type="number" name="number" placeholder="Номер договора" label="Номер договора" />
            <x-form-input type="number" name="amount_price" placeholder="Общая стоимость" label="Общая стоимость" />
            <x-form-input type="text" name="comment" placeholder="Комментарий" label="Комментарий" />
        </div>

        <div class="input-group input-group-sm mb-3">
            <span class="input-group-text" id="inputGroup-sizing-sm">Выберите услугу</span>
            <select name="service_id" class="form-select" aria-label="Выберите услугу">
                @foreach ($services as $service)
                    <option value="{{ $service->id }}">{{ $service->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="input-group input-group-sm mb-3">
            <span class="input-group-text" id="inputGroup-sizing-sm">Общая стоимость</span>
            <input type="number" name="amount_price" class="form-control" value="{{ old('amount_price') }}"
                aria-label="Общая стоимость" aria-describedby="inputGroup-sizing-sm">
        </div>
        <div class="payments-wrapper">
            <div class="input-group input-group-sm mb-3 payment-group">
                <span class="input-group-text" id="inputGroup-sizing-sm">Платеж 1</span>
                <input type="number" name="payments[]" class="form-control" aria-label="Платеж"
                    aria-describedby="inputGroup-sizing-sm">
            </div>
        </div>
        <button type="button" id="add-payment" class="btn btn-secondary mb-3">Добавить платеж</button>
        <button type="submit" class="btn btn-primary">
            Создать
        </button>
    </form>
@endsection
