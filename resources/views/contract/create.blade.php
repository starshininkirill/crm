@extends('layouts.contract')

@section('content')
    <h1>Создать договор</h1>
    <form action="{{ route('contract.store') }}" method="POST" class="contract-create-form">
        @csrf
        <div class="input-group input-group-sm mb-3">
            <span class="input-group-text" id="inputGroup-sizing-sm">Данные клиента</span>
            <input name="client" type="text" class="form-control" aria-label="Данные клиента"
                aria-describedby="inputGroup-sizing-sm">
        </div>
        <div class="input-group input-group-sm mb-3">
            <span class="input-group-text" id="inputGroup-sizing-sm">Общая стоимость</span>
            <input type="number" name="amount_price" class="form-control" aria-label="Общая стоимость"
                aria-describedby="inputGroup-sizing-sm">
        </div>
        <button type="submit" class="btn btn-primary">
            Создать
        </button>
    </form>
@endsection
