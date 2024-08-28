@extends('admin.layouts.departments.sale')

@section('content')
    <h1 class="text-4xl font-semibold mb-6">{{ $department->departmentable->name }}</h1>
    @if ($department->parent)
        <h2 class="text-4xl mb-5 font-semibold">Родительский отдел</h2>
        <div class="mb-7">
            <a href="{{ route('admin.department.show', $department->parent->id) }}" class=" text-lg text-blue-600">
                {{ $department->parent->departmentable->name }}
            </a>
        </div>
    @endif

    @if (!$department->childDepartments->isEmpty())
        <h2 class="text-4xl mb-5 font-semibold">Подотделы</h2>
        <div class="flex flex-col gap-1 mb-7">
            @foreach ($department->childDepartments as $childDepartment)
                <a href="{{ route('admin.department.show', $childDepartment->id) }}" class=" text-lg text-blue-600">
                    {{ $childDepartment->departmentable->name }}
                </a>
            @endforeach
        </div>
    @endif
    @if (!$department->positions->isEmpty())
        <h2 class="text-4xl mb-5 font-semibold">Должности</h2>
        <div class="flex flex-col gap-1 mb-7">
            @foreach ($department->positions as $position)
                <div class=" text-lg">
                    {{ $position->name }}
                </div>
            @endforeach
        </div>
    @endif

    @if ($department->users())
        <h2 class="text-4xl mb-5 font-semibold">Сотрудники</h2>
        <div class="flex flex-col gap-2">
            @foreach ($department->users() as $user)
                <a href="{{ route('admin.user.show', $user->id) }}" class=" border-b pb-2 flex flex-col gap-1">
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
