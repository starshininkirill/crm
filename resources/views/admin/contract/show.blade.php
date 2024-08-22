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
        <div class=" w-1/3">
            <h2 class="text-4xl mb-4">Исполнители</h2>

            @if (session('success'))
                <div class="mb-3 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative"
                    role="alert">
                    <span class="block sm:inline">{{ session('success') }}</span>
                </div>
            @endif
            @if ($errors->any())
                <ul class="flex flex-col gap-1 mb-4">
                    @foreach ($errors->all() as $error)
                        <li class="text-red-400">{{ $error }}</li>
                    @endforeach
                </ul>
            @endif

            <div class="flex flex-col gap-3 mb-7">
                @if (!$performersData->isEmpty())
                    @foreach ($performersData as $data)
                        <div class="flex flex-col gap-2 text-xl font-semibold">
                            <div class="flex flex-col gap-1">
                                <div class="text-2xl font-semibold">
                                    {{ $data['role']->name }}:
                                </div>
                                @foreach ($data['performers'] as $performer)
                                    <div class=" text-lg font-normal">
                                        {{ $performer->first_name }} {{ $performer->last_name }}
                                    </div>
                                @endforeach
                            </div>

                            <form action="{{ route('admin.contract.attachUser', $contract->id) }}" method="post"
                                class="flex flex-col gap-3">
                                @csrf
                                <input type="hidden" name="role_in_contracts_id" value="{{ $data['role']->id }}">
                                <select id="user_id" name="user_id" class="select">
                                    <option disabled selected value="">
                                        Выберите исполнителя
                                    </option>
                                    @foreach ($data['role']->getPerformers() as $performer)
                                        <option value="{{ $performer->id }}">
                                            {{ $performer->first_name }} {{ $performer->last_name }}
                                        </option>
                                    @endforeach
                                </select>
                                <button class="btn">
                                    Добавить
                                </button>
                            </form>
                        </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>
@endsection
