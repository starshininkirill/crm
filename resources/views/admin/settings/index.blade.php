@extends('admin.layouts.settings')

@section('content')
    <h1 class="text-4xl font-semibold mb-6">Основные настройки</h1>
    @if (!$serviceCategories->isEmpty())
        <div class="grid grid-cols-2 gap-4">
            <div>
                <div class="text-2xl font-semibold mb-3">
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
                    <input type="hidden" name="name" value="contract_main_categories">
                    <button class="btn mt-3">
                        Изменить
                    </button>
                </form>
            </div>
            <div>
                <div class="text-2xl font-semibold mb-3">
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
                    <input type="hidden" name="name" value="contract_secondary_categories">
                    <button class="btn mt-3">
                        Изменить
                    </button>
                </form>
            </div>
        </div>
    @endif
@endsection
