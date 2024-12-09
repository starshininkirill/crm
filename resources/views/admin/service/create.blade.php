@extends('admin.layouts.service')

@section('content')
    <h1 class="text-4xl font-semibold mb-6">Создать услугу</h1>

    <div class="flex gap-20">
        <form method="POST" class=" max-w-md shrink-0 " action="{{ route('service.store') }}">
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


            <div class="flex flex-col gap-2">
                <x-form-input required type="text" name="name" placeholder="Название услуги" label="Название услуги"
                    value="{{ old('name') }}" />
                <x-form-textarea name="description" placeholder="Введите описание" label="Описание"
                    value="{{ old('description') }}" required="true" />
                <x-form-input type="text" name="work_days_duration" placeholder="5 ( пять ) рабочих дней"
                    label="Срок исполнения" value="{{ old('work_days_duration') }}" />
                <x-form-input required type="number" name="price" placeholder="20000" label="Рекомендованая цена"
                    value="{{ old('price') }}" />

                <x-id-select-input :options="$categories" label="Выберите категорию" name="service_category_id"
                    id="service_category_id" />

                <div class="font-semibold my-3">
                    ID шаблонов для генератора документов( необязательно )
                </div>

                <x-form-input type="number" name="law_default" placeholder="Юр. лицо одна услуга"
                    label="Юр. лицо одна услуга" value="{{ old('law_default') }}" />
                <x-form-input type="number" name="law_complex" placeholder="Юр. лицо комплекс" label="Юр. лицо комплекс"
                    value="{{ old('law_complex') }}" />
                <x-form-input type="number" name="physic_default" placeholder="Физ. лицо одна услуга"
                    label="Физ. лицо одна услуга" value="{{ old('physic_default') }}" />
                <x-form-input type="number" name="physic_complex" placeholder="Физ. лицо комплекс"
                    label="Физ. лицо комплекс" value="{{ old('physic_complex') }}" />

                <button type="submit"
                    class="middle w-full none center mr-4 rounded-lg bg-blue-500 py-3 px-6 font-sans text-xs font-bold uppercase text-white shadow-md shadow-blue-500/20 transition-all hover:shadow-lg hover:shadow-blue-500/40 focus:opacity-[0.85] focus:shadow-none active:opacity-[0.85] active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
                    data-ripple-light="true">
                    Создать
                </button>
            </div>
        </form>
        @if ($contractTemplateIdsText != null)
            <div class="flex flex-col gap-2">
                <div class="text-xl font-semibold mb-3">
                    ID Шаблонов
                </div>
                <div class="styled-content">
                    {!! $contractTemplateIdsText->value !!}
                </div>
            </div>
        @endif
    </div>
@endsection
