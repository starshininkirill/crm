@extends('admin.layouts.department')

@section('content')
    <h1 class="text-4xl font-semibold mb-6">Создать Должность</h1>
    @if ($departments->isEmpty())
        <h2 class="text-xl">Для начала создайте отделы</h2>
    @else
        <form method="POST" class=" max-w-md " action="{{ route('admin.department.position.store') }}">
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
                <x-form-input type="text" name="name" placeholder="Название должности" label="Название должности" />
                <x-form-input type="number" name="salary" placeholder="Ставка" label="Ставка" />
                <x-select-input :options="$departments" label="Выберите отдел" name="department_id" id="department_id" />

                <button type="submit"
                    class="middle w-full none center mr-4 rounded-lg bg-blue-500 py-3 px-6 font-sans text-xs font-bold uppercase text-white shadow-md shadow-blue-500/20 transition-all hover:shadow-lg hover:shadow-blue-500/40 focus:opacity-[0.85] focus:shadow-none active:opacity-[0.85] active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
                    data-ripple-light="true">
                    Создать
                </button>
            </div>
        </form>
    @endif
@endsection