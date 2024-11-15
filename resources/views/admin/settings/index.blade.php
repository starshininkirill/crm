@extends('admin.layouts.settings')

@section('content')
    <h1 class="text-4xl font-semibold mb-6">Основные настройки</h1>
    <div class="text-2xl font-semibold mb-3">
        Основные категории услуг
    </div>
    @if (!$serviceCategories->isEmpty())
        <form method="POST" class="flex flex-col gap-1">
            @csrf
            @foreach ($serviceCategories as $category)
                <label class=" cursor-pointer">
                    <input name="categories" type="checkbox" value="{{ $category->id }}">
                    {{ $category->name }}
                </label>
            @endforeach
            <button class="btn mt-3">
                Изменить
            </button>
        </form>
    @endif
@endsection
