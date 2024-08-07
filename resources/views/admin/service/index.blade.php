@extends('admin.layouts.service')

@section('content')
    <h1 class="text-4xl font-semibold mb-6">Услуги</h1>

    @if ($services->isEmpty())
        <h2 class="text-xl">Услуг не найдено</h2>
    @else
        <div class="flex flex-col gap-3">
            @foreach ($services as $service)
                <div class="p-4 text-xl border ">
                    {{ $service->name }}
                </div>
            @endforeach
        </div>
    @endif
@endsection
