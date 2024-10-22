@extends('admin.layouts.departments.sale')

@section('content')
    <h1 class="text-4xl font-semibold mb-6">Настройка отчётов</h1>
    <div class="flex flex-col gap-2 w-fit">
        <div class=" text-lg">
            Отчёт за месяц
        </div>
        @foreach ($plans as $plan)
            <div class="plan flex gap-1 border py-1 px-2">
                <form method="POST" action="">
                    <input name="mounth" type="number" value="{{ $plan['mounth'] }}">
                    <input name="goal" type="number" value="{{ $plan['goal'] }}">
                    <button>Изменить</button>
                </form>
                <form method="DELETE" action="">
                    <button>
                        Удалить
                    </button>
                </form>
            </div>
        @endforeach
    </div>

@endsection
