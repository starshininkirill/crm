@extends('admin.layouts.service')

@section('content')
    <h1 class="text-4xl font-semibold mb-6">Категории услуг</h1>

    @if ($categories->isEmpty())
        <h2 class="text-xl">Категорий услуг не найдено</h2>
    @else
        <div class="flex flex-col gap-3">
            @foreach ($categories as $category)
                <div class="p-4 text-xl border ">
                    {{ $category->name }}
                </div>
            @endforeach
        </div>
    @endif
@endsection
