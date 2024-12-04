@extends('admin.layouts.user')

@section('content')
    <h1 class="text-4xl font-semibold mb-6">Создать сотрудника</h1>

    <form method="POST" class=" max-w-md " action="{{ route('admin.user.store') }}">
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
            <x-form-input type="text" name="first_name" placeholder="Имя" label="Имя" />
            <x-form-input type="text" name="last_name" placeholder="Фамилия" label="Фамилия" />
            <x-form-input type="email" name="email" placeholder="Почта" label="Почта" />
            <x-form-input type="password" name="password" placeholder="******" label="Пароль" />
            <x-form-input type="password" name="password2" placeholder="******" label="Повторите пароль" />
            <x-id-select-input :options="$positions" label="Должность" name="position_id" id="position_id" />

            <button class="btn" type="submit">
                Создать
            </button>
        </div>
    </form>
@endsection
