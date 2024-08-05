@extends('admin.layouts.user')

@section('content')
    <h1 class="text-4xl font-semibold mb-6">Сотрудники</h1>

    @if ($users->isEmpty())
        <h2 class="text-2xl mb-4">Сотрудники не найдены</h2>
    @else
        <div class="flex flex-col gap-3">
            @foreach ($users as $user)
                <a>
                    {{ $user }}
                </a>
            @endforeach
        </div>
    @endif
@endsection
