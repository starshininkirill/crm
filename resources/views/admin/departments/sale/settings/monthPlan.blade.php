<div class="flex flex-col gap-2 w-fit">
    <div class=" text-2xl font-semibold mb-4">
        План на месяц
    </div>
    <div class="flex flex-col gap-2 mb-4">
        @if (!$plans->has($workPlanClass::MOUNTH_PLAN) || $plans[$workPlanClass::MOUNTH_PLAN]->isEmpty())
            <div class=" text-xl mb-4">
                Нет планов
            </div>
        @else
            @foreach ($plans[$workPlanClass::MOUNTH_PLAN] as $plan)
                <div class="plan flex gap-4 border-b-2 py-1 pb-3 w-full items-center">
                    <form class="flex gap-4" method="POST" action="{{ route('workPlan.update', $plan->id) }}">
                        @method('PUT')
                        @csrf
                        <label class="flex gap-2 items-center" for="month">
                            Месяц
                            <input {{ $isCurrentMonth ? '' : 'disabled' }} class="input" name="month" type="number"
                                value="{{ $plan['month'] }}">
                        </label>
                        <label class="flex gap-2 items-center" for="goal">
                            Цель
                            <input {{ $isCurrentMonth ? '' : 'disabled' }} class="input" name="goal" type="number"
                                value="{{ $plan['goal'] }}">
                        </label>
                        @if ($isCurrentMonth)
                            <button class=" hover:text-blue-400">
                                Изменить
                            </button>
                        @endif
                    </form>
                    @if ($isCurrentMonth)
                        <form method="POST" action="{{ route('workPlan.destroy', $plan->id) }}">
                            @method('DELETE')
                            @csrf
                            <button class="hover:text-red-500">
                                Удалить
                            </button>
                        </form>
                    @endif
                </div>
            @endforeach
        @endif
    </div>
    @if ($isCurrentMonth)
        <form method="POST" action="{{ route('workPlan.store') }}">
            @csrf
            <div class=" text-xl mb-2">
                Новый план
            </div>
            @csrf
            <div class="flex flex-col gap-2">
                <input type="hidden" name="type" value="{{ $workPlanClass::MOUNTH_PLAN }}">
                <input type="hidden" name="department_id" value="{{ $departmentId }}">
                <input type="hidden" name="created_at" value="{{ $date->format('Y-m-d') }}">
                <label class="flex gap-2 items-center" for="month">
                    Месяц
                    <input class="input" name="month" type="number">
                </label>
                <label class="flex gap-2 items-center" for="goal">
                    Цель
                    <input class="input" name="goal" type="number">
                </label>
                <button class="btn">
                    Создать
                </button>
            </div>
        </form>
    @endif
</div>
