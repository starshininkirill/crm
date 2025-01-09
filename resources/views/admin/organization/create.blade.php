@extends('admin.layouts.organization')

@section('content')
    <h1 class="text-4xl font-semibold mb-6">Создать организацию</h1>

    <form method="POST" class=" max-w-md shrink-0 " action="{{ route('organization.store') }}">
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


        <div class="flex flex-col gap-2">
            <x-form-input required type="text" name="short_name" placeholder="ИП 1" label="Краткое наименование организации"
                value="{{ old('short_name') }}" />
            <x-form-input required type="text" name="name" placeholder="Индивидуальный предпиниматель Иванов Иван Иванович" label="Полное наименование организации"
                value="{{ old('name') }}" />
            <x-form-input required type="number" name="inn" placeholder="ИНН" label="ИНН"
                value="{{ old('inn') }}" />

            
            <button type="submit"
                class="middle w-full none center mr-4 rounded-lg bg-blue-500 py-3 px-6 font-sans text-xs font-bold uppercase text-white shadow-md shadow-blue-500/20 transition-all hover:shadow-lg hover:shadow-blue-500/40 focus:opacity-[0.85] focus:shadow-none active:opacity-[0.85] active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
                data-ripple-light="true">
                Создать
            </button>
        </div>
    </form>
@endsection
