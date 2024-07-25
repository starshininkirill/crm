@extends('layouts.contract')

@section('content')
    <h1>Создать договор</h1>
    <form action="{{ route('contract.store') }}" method="POST" class="contract-create-form d-flex flex-column">
        @csrf
        @if ($errors->any())
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        <div class="input-group input-group-sm mb-3">
            <span class="input-group-text" id="inputGroup-sizing-sm">Данные клиента</span>
            <input name="client" type="text" class="form-control" value="{{ old('client') }}" aria-label="Данные клиента"
                aria-describedby="inputGroup-sizing-sm">
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
            <input type="number" name="amount_price" class="form-control" value="{{ old('amount_price') }}" aria-label="Общая стоимость"
                aria-describedby="inputGroup-sizing-sm">
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
