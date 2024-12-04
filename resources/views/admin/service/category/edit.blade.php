@extends('admin.layouts.service')

@section('content')
    <h1 class="text-4xl font-semibold mb-6">Редактировать категорию</h1>

    <form class=" max-w-md" method="POST" class="" action="{{ route('serviceCategory.update', $serviceCategory->id) }}">
        @method('PUT')
        @csrf
        @if (session('success'))
            <div class="mb-3 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
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
            <x-form-input type="text" value="{{ $serviceCategory->name }}" name="name"
                placeholder="Название категории" label="Название категории" />
            @if (!$types->isEmpty())
                <div>

                    <select id="type" name="type" class="select">
                        <option {{ $serviceCategory->type == null ? 'selected' : '' }} value="">
                            Нет типа
                        </option>
                        @foreach ($types as $key => $value)
                            <option {{ $key === $serviceCategory->type ? 'selected' : '' }} value="{{ $key }}">
                                {{ $value }} 
                            </option>
                        @endforeach
                    </select>
                </div>
            @endif

            <button type="submit"
                class="middle w-full none center mr-4 rounded-lg bg-blue-500 py-3 px-6 font-sans text-xs font-bold uppercase text-white shadow-md shadow-blue-500/20 transition-all hover:shadow-lg hover:shadow-blue-500/40 focus:opacity-[0.85] focus:shadow-none active:opacity-[0.85] active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
                data-ripple-light="true">
                Изменить
            </button>
        </div>
    </form>
@endsection
