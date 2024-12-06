@extends('admin.layouts.service')

@section('content')
    <h1 class="text-4xl font-semibold mb-6">Редактировать услугу</h1>

    <form method="POST" class="max-w-md" action="{{ route('service.update', $service->id) }}">
        @csrf
        @method('PUT')
        @if (session('success'))
            <div class="mb-3 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded w-fit relative"
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

        <div class="flex flex-col gap-4">
            <x-form-input required type="text" name="name" placeholder="Название услуги" label="Название услуги"
                value="{{ $service->name }}" />
            <x-form-textarea name="description" placeholder="Введите описание" label="Описание"
                value="{{ $service->description }}" required="true" />
            <x-form-input type="text" name="work_days_duration" placeholder="5 ( пять ) рабочих дней"
                label="Срок исполнения" value="{{ $service->work_days_duration }}" />
            <x-form-input required type="number" name="price" placeholder="20000" label="Рекомендованая цена"
                value="{{ $service->price }}" />

            <div>
                <label for="service_category_id" class="mb-1 block text-sm font-medium leading-6 text-gray-900">
                    Категория
                </label>
                <select id="service_category_id" name="service_category_id" class="select">
                    <option {{ !isset($service->category) ? 'selected' : '' }} disabled value="">
                        Выберите категорию
                    </option>
                    @foreach ($categories as $category)
                        <option {{ isset($service->category) && $category->id == $service->category->id ? 'selected' : '' }}
                            value="{{ $category->id }}">
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="font-semibold my-3">
                ID шаблонов для генератора документов( необязательно )
            </div>

            @if (!empty($service->deal_template_ids))
                <x-form-input type="number" name="law_default" placeholder="Юр. лицо одна услуга"
                    label="Юр. лицо одна услуга"
                    value="{{ json_decode($service->deal_template_ids, true)['law_default'] }}" />
                <x-form-input type="number" name="law_complex" placeholder="Юр. лицо комплекс" label="Юр. лицо комплекс"
                    value="{{ json_decode($service->deal_template_ids, true)['law_complex'] }}" />
                <x-form-input type="number" name="physic_default" placeholder="Физ. лицо одна услуга"
                    label="Физ. лицо одна услуга"
                    value="{{ json_decode($service->deal_template_ids, true)['physic_default'] }}" />
                <x-form-input type="number" name="physic_complex" placeholder="Физ. лицо комплекс"
                    label="Физ. лицо комплекс"
                    value="{{ json_decode($service->deal_template_ids, true)['physic_complex'] }}" />
            @else
                <x-form-input type="number" name="law_default" placeholder="Юр. лицо одна услуга"
                    label="Юр. лицо одна услуга"
                    value="{{ !empty($service->deal_template_ids) && array_key_exists('law_default', $service->deal_template_ids) ? $service->deal_template_ids['law_default'] : '' }}" />
                <x-form-input type="number" name="law_complex" placeholder="Юр. лицо комплекс" label="Юр. лицо комплекс"
                    value="{{ old('law_complex') }}" />
                <x-form-input type="number" name="physic_default" placeholder="Физ. лицо одна услуга"
                    label="Физ. лицо одна услуга" value="{{ old('physic_default') }}" />
                <x-form-input type="number" name="physic_complex" placeholder="Физ. лицо комплекс"
                    label="Физ. лицо комплекс" value="{{ old('physic_complex') }}" />
            @endif

            <button type="submit"
                class="middle w-full none center mr-4 rounded-lg bg-blue-500 py-3 px-6 font-sans text-xs font-bold uppercase text-white shadow-md shadow-blue-500/20 transition-all hover:shadow-lg hover:shadow-blue-500/40 focus:opacity-[0.85] focus:shadow-none active:opacity-[0.85] active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
                data-ripple-light="true">
                Изменить
            </button>
        </div>
    </form>
@endsection
