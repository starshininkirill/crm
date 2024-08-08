@extends('admin.layouts.user')

@section('content')
    <h1 class="text-4xl font-semibold mb-6">Сотрудники</h1>

    @if ($users->isEmpty())
        <h2 class="text-2xl mb-4">Сотрудники не найдены</h2>
    @else
        <div class="flex flex-col gap-2">
            @foreach ($users as $user)
                <a href="#" class=" border-b pb-2 flex flex-col gap-1">
                    <div class=" font-semibold text-xl">
                        {{ $user->first_name }}
                        {{ $user->last_name }}
                    </div>
                    <div>
                        {{ $user->email }}
                    </div>
                    <div class="div">
                        @if ($user->position)
                            Должность: {{ $user->position->name }}
                        @endif
                    </div>
                </a>
            @endforeach
        </div>
    @endif
@endsection
