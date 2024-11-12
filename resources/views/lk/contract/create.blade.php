@extends('lk.base')

@section('content')
    <div>
        <h1 class=" text-4xl font-bold mb-5">
            Создание Договора
        </h1>
        @if (session('success'))
            <div class="mb-3 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
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

        <vue-contract-create-form action="{{ route('contract.store') }}" token="{{ csrf_token() }}" />

        {{-- <form action="{{ route('contract.store') }}" method="POST" class=""> --}}
        @csrf

        <fieldset class="grid grid-cols-2 gap-4 max-w-xl mb-6">
            <x-form-input type="number" name="leed" placeholder="Лид" label="Лид" />
            <x-form-input type="number" name="contract_number" placeholder="Номер договора" label="Номер договора" />
            <x-form-input type="text" name="contact_fio" placeholder="ФИО" label="ФИО" />
            <x-form-input type="tel" name="phone" placeholder="Телефон" label="Телефон" />
        </fieldset>

        <div class="flex flex-col w-full mb-6">
            <div class="flex flex-col rounded-md border border-gray-400 shadow-xl">
                <div class="bg-gray-800 p-2 rounded-md text-white font-semibold text-xl cursor-pointer">
                    Контрагент
                </div>
                <div class="flex flex-col gap-4 p-2 mt-2">
                    <div class="flex flex-col gap-2">
                        <div class="text-xl font-semibold">
                            Контрагент
                        </div>

                        <div class="grid grid-cols-2 w-fit gap-5">
                            <label class=" cursor-pointer">
                                <input checked type="radio" value="fizic" name="client_type"
                                    placeholder="Физическое лицо">
                                Физическое лицо
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" value="uric" name="client_type" placeholder="Юридическое лицо">
                                Юридическое лицо
                            </label>
                        </div>
                    </div>

                    <div class="flex flex-col gap-2">
                        <div class="text-xl font-semibold">
                            Тип оплаты
                        </div>

                        <div class="grid grid-cols-2 w-fit gap-5">
                            <label class=" cursor-pointer">
                                <input checked type="radio" value="0" name="tax">
                                Без НДС
                            </label>
                            <label class="cursor-pointer">
                                <input type="radio" value="1" name="tax">
                                С НДС
                            </label>
                        </div>
                    </div>

                    <fieldset id="fizik-fieldset" class="flex flex-col gap-2">
                        <div class="text-xl font-semibold">
                            Данные для Физического лица
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <x-form-input type="text" name="fio" placeholder="ФИО" label="ФИО" />
                            <x-form-input type="number" name="pasport_seria" placeholder="Серия паспорта"
                                label="Серия паспорта" />
                            <x-form-input type="number" name="pasport_number" placeholder="Номер паспорта"
                                label="Номер паспорта" />
                            <x-form-input type="text" name="pasport_who" placeholder="Паспорт кем выдан"
                                label="Паспорт кем выдан" />
                            <x-form-input type="text" name="address" placeholder="Адрес регистрации"
                                label="Адрес регистрации" />
                        </div>
                    </fieldset>

                    <fieldset id="ur-fieldset" class="flex flex-col gap-2">
                        <div class="text-xl font-semibold">
                            Данные для Юридического лица
                        </div>

                        <div class="grid grid-cols-2 gap-3 mb-2">
                            <x-form-input type="text" name="full_corp_name" placeholder="Полное название организации"
                                label="Полное название организации" />
                            <x-form-input type="text" name="short_corp_name"
                                placeholder="Кратное наименование организации" label="Кратное наименование организации" />
                        </div>

                        <div class="text-xl font-semibold">
                            ОГРН или ОГРНИП
                        </div>
                        <div class="grid grid-cols-2 gap-3 mb-2">
                            <x-form-input type="text" name="ogrn" placeholder="Номер ОГРН/ОГРНИП"
                                label="Номер ОГРН/ОГРНИП" />
                            <x-form-input type="text" name="director_name" placeholder="(Иванова Ивана Ивановича)"
                                label="ФИО Ген.дира в РОД ПАДЕЖЕ" />
                            <x-form-input type="text" name="ur_address" placeholder="Юридический адрес"
                                label="Юридический адрес" />
                            <x-form-input type="number" name="inn" placeholder="ИНН/КПП" label="ИНН/КПП" />
                            <x-form-input type="number" name="payment_account" placeholder="Расчётный счёт"
                                label="Расчётный счёт" />
                            <x-form-input type="number" name="сorrespondent_account"
                                placeholder="Корреспондентский счёт" label="Корреспондентский счёт" />
                            <x-form-input type="number" name="bank_name" placeholder="Наименование банка"
                                label="Наименование банка" />
                            <x-form-input type="number" name="bank_bik" placeholder="БИК Банка" label="БИК Банка" />
                        </div>
                    </fieldset>

                </div>
            </div>
        </div>

        <button class="btn">
            Отправить
        </button>
        </form>
    </div>
@endsection
