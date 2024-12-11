@extends('admin.layouts.settings')

@section('content')
    {{-- @vite(['resources/js/tinymce.js']) --}}
    <h1 class="text-4xl font-semibold mb-6">Основные настройки</h1>
    @if (!$serviceCategories->isEmpty())
        <div class="grid grid-cols-2 gap-4 gap-y-7">
            <div>
                <div class="text-xl font-semibold mb-3">
                    Основные категории услуг ( Первая услуга в генераторе документов )
                </div>
                <form
                    action="{{ $mainCategoriesOption == null ? route('option.store') : route('option.update', $mainCategoriesOption->id) }}"
                    method="POST" class="flex flex-col gap-1">
                    @csrf
                    @if ($mainCategoriesOption != null)
                        @method('PUT')
                    @endif
                    @foreach ($serviceCategories as $category)
                        @php
                            if ($mainCategoriesOption == null) {
                                $checked = false;
                            } else {
                                $checked = in_array($category->id, json_decode($mainCategoriesOption->value));
                            }
                        @endphp
                        <label class=" cursor-pointer">
                            <input name="value[]" type="checkbox" {{ $checked == true ? 'checked' : '' }}
                                value="{{ $category->id }}">
                            {{ $category->name }}
                        </label>
                    @endforeach
                    <input type="hidden" name="name" value="contract_generator_main_categories">
                    <button class="btn mt-3">
                        Изменить
                    </button>
                </form>
            </div>
            <div>
                <div class="text-xl font-semibold mb-3">
                    Дополнительные категории услуг ( 2 и далее услуги в генераторе документов )
                </div>
                <form
                    action="{{ $secondaryCategoriesOption == null ? route('option.store') : route('option.update', $secondaryCategoriesOption->id) }}"
                    method="POST" class="flex flex-col gap-1">
                    @csrf
                    @if ($secondaryCategoriesOption != null)
                        @method('PUT')
                    @endif
                    @foreach ($serviceCategories as $category)
                        @php
                            if ($secondaryCategoriesOption == null) {
                                $checked = false;
                            } else {
                                $checked = in_array($category->id, json_decode($secondaryCategoriesOption->value));
                            }
                        @endphp
                        <label class=" cursor-pointer">
                            <input name="value[]" type="checkbox" {{ $checked == true ? 'checked' : '' }}
                                value="{{ $category->id }}">
                            {{ $category->name }}
                        </label>
                    @endforeach
                    <input type="hidden" name="name" value="contract_generator_secondary_categories">
                    <button class="btn mt-3">
                        Изменить
                    </button>
                </form>
            </div>
            <div>
                <div class="text-xl font-semibold mb-3">
                    Услуги, которым нужно кол-во страниц
                </div>
                <form
                    action="{{ $needSeoPages == null ? route('option.store') : route('option.update', $needSeoPages->id) }}"
                    method="POST" class="grid grid-cols-2 gap-2">
                    @csrf
                    @if ($needSeoPages != null)
                        @method('PUT')
                    @endif
                    @foreach ($services as $service)
                        @php
                            if ($needSeoPages == []) {
                                $checked = false;
                            } else {
                                $checked = in_array($service->id, json_decode($needSeoPages->value));
                            }
                        @endphp
                        <label class=" cursor-pointer">
                            <input name="value[]" type="checkbox" {{ $checked == true ? 'checked' : '' }}
                                value="{{ $service->id }}">
                            {{ $service->name }}
                        </label>
                    @endforeach
                    <input type="hidden" name="name" value="contract_generator_secondary_categories">
                </form>
                <button class="btn mt-3">
                    Изменить
                </button>
            </div>
            <div>
                <div class="text-xl font-semibold mb-3">
                    НДС
                </div>
                <form action="{{ $taxNds == null ? route('option.store') : route('option.update', $taxNds->id) }}"
                    method="POST" class="flex flex-col gap-1">
                    @csrf
                    @if ($taxNds != null)
                        @method('PUT')
                    @endif
                    <x-form-input type="number" name="value" value="{{ $taxNds->value ?? 0 }}" placeholder="20%"
                        label="Ставка НДС ( % )" />
                    <input type="hidden" name="name" value="tax_nds">
                    <button class="btn mt-3">
                        Изменить
                    </button>
                </form>
            </div>
            <div>
                <div class="text-xl font-semibold mb-3">
                    Шаблон для Юр лица генератора Платежей по умолчанию
                </div>
                <form action="{{ $paymentDefaultLawTemplate == null ? route('option.store') : route('option.update', $paymentDefaultLawTemplate->id) }}"
                    method="POST" class="flex flex-col gap-1">
                    @csrf
                    @if ($paymentDefaultLawTemplate != null)
                        @method('PUT')
                    @endif
                    <x-form-input type="number" name="value" value="{{ $paymentDefaultLawTemplate->value ?? 0 }}" placeholder="198"
                        label="Шаблон по умолчанию" />
                    <input type="hidden" name="name" value="payment_generator_default_law_template">
                    <button class="btn mt-3">
                        Изменить
                    </button>
                </form>
            </div>
            <div>
            </div>
            <div>
                <div class="text-xl font-semibold mb-3">
                    Описание условий для сделки с РК
                </div>
                <form
                    action="{{ $contractRkText == null ? route('option.store') : route('option.update', $contractRkText->id) }}"
                    method="POST" class="flex flex-col gap-1">
                    @csrf
                    @if ($contractRkText != null)
                        @method('PUT')
                    @endif
                    <textarea name="value" id="tinyredactor" value="{{ $contractRkText->value ?? '' }}"
                        class="block w-full rounded-md border-0 py-1.5 px-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 h-32 resize-none"
                        placeholder="Условия..." label="Условия..."></textarea>
                    <input type="hidden" name="name" value="contract_generator_secondary_categories">
                    <button class="btn mt-3">
                        Изменить
                    </button>
                </form>
            </div>
            <div>
                <div class="text-xl font-semibold mb-3">
                    ID Шаблонов для генератора договора
                </div>
                <form
                    action="{{ $contractTemplateIds == null ? route('option.store') : route('option.update', $contractTemplateIds->id) }}"
                    method="POST" class="flex flex-col gap-1">
                    @csrf
                    @if ($contractTemplateIds != null)
                        @method('PUT')
                    @endif
                    <input type="hidden" name="name" value="contract_generator_template_ids_text">
                    <textarea name="value" id="tinyredactor" value="{{ $contractTemplateIds->value ?? '' }}"
                        class="block w-full rounded-md border-0 py-1.5 px-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6 h-32 resize-none"
                        placeholder="Перевод шаблонов в текст" label="Перевод шаблонов в текст"></textarea>
                    <button class="btn mt-3">
                        Изменить
                    </button>
                </form>
            </div>
        </div>
    @endif
@endsection
