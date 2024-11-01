@extends('admin.layouts.department')

@section('content')
    <h1 class="text-4xl font-semibold mb-6">Отделы</h1>

    @if ($departments->isEmpty())
        <h2 class="text-xl">Отделы не найдены</h2>
    @else
        <div class="flex flex-col gap-3">
            @foreach ($departments as $department)
                @php
                    $childsDepartments = $department->childDepartments;
                    $positions = $department->positions;
                @endphp
                <div class="p-4 text-xl border">
                    <a href="{{ route('admin.department.show', $department->id) }}" class="text-xl">
                        {{ $department->name }}
                    </a>
                    @if ($childsDepartments->isNotEmpty())
                        <div class="font-bold text-lg mb-1 mt-3 pl-3">
                            Подотделы
                        </div>
                        <ul class="flex flex-col gap-1 pl-3">
                            @foreach ($childsDepartments as $childDepartment)
                                <li class=" text-lg list-disc list-inside">
                                    <a href="{{ route('admin.department.show', $childDepartment->id) }}">
                                        {{ $childDepartment->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                    @if ($positions->isNotEmpty())
                        <div class="font-bold text-lg mb-1 mt-3 pl-3">
                            Должности
                        </div>
                        <ul class="flex flex-col gap-1 pl-3">
                            @foreach ($positions as $position)
                                <li class=" text-lg list-disc list-inside">
                                    {{ $position->name }}
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
@endsection
