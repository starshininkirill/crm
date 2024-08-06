@extends('base')

@section('main')
    <form method="POST" class=" p-6 border rounded-md max-w-md w-full m-auto " action="{{ route('login.attempt') }}">
        @csrf
        <h2 class=" mb-5 text-4xl font-bold">
            Вход
        </h2>
        @if ($errors->any())
            <ul class="flex flex-col gap-1 mb-4">
                @foreach ($errors->all() as $error)
                    <li class="text-red-400">{{ $error }}</li>
                @endforeach
            </ul>
        @endif
        <div class="flex flex-col gap-2">
            <x-form-input type="email" name="email" placeholder="Почта" label="Почта" value="{{ old('email') }}" />
            <x-form-input type="password" name="password" placeholder="******" label="Пароль" />
            <button type="submit"
                class="mt-4 middle w-full none center mr-4 rounded-lg bg-blue-500 py-3 px-6 font-sans text-xs font-bold uppercase text-white shadow-md shadow-blue-500/20 transition-all hover:shadow-lg hover:shadow-blue-500/40 focus:opacity-[0.85] focus:shadow-none active:opacity-[0.85] active:shadow-none disabled:pointer-events-none disabled:opacity-50 disabled:shadow-none"
                data-ripple-light="true">
                Войти
            </button>
        </div>
    </form>
@endsection
