@extends('admin.layouts.contract')

@section('content')
    <h1 class="text-4xl font-semibold mb-6">Договор: №{{ $contract->number }}</h1>
    <div class="flex gap-4">
        <div class="w-2/3 info">
            <h4 class="text-2xl mb-5">Цена: {{ $contract->getPrice() }}</h4>
            <h4 class="text-2xl mb-5">
                Услуги:
                @if ($contract->services->isEmpty())
                    Услуги пустые
                @else
                    @foreach ($contract->services as $service)
                        {{ $service->name }}
                        @if (!$loop->last)
                            ,
                        @endif
                    @endforeach
                @endif
            </h4>
            @if (!empty($contract->payments))
                <h3>Платежи</h3>
                <div class="payments d-flex flex-column gap-1">
                    @foreach ($contract->payments as $payment)
                        <a href="{{ route('admin.payment.show', $payment->id) }}" class="payment">
                            {{ $payment->order }}й платеж: {{ $payment->value }}
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
        <form action="" class=" w-1/3">
            <h2 class="text-4xl mb-4">Исполнители</h2>

            <div class="flex flex-col gap-3 mb-7">
                @if (!$roles->isEmpty())
                    @foreach ($roles as $role)
                        <div class="flex flex-col gap-2 text-xl font-semibold">
                            {{ $role->name }}
                            <select id="" name="" class="select">
                                <option selected value="">
                                    Выберите исполнителя
                                </option>
                                @foreach ($role->getPerformers() as $performer)
                                    <option value="{{ $performer->id }}">
                                        {{ $performer->first_name }} {{ $performer->last_name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    @endforeach
                @endif
            </div>
            <button type="submit" class="btn">
                Изменить
            </button>
        </form>
    </div>
@endsection
