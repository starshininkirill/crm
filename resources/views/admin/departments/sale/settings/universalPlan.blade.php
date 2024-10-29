<div class="flex flex-col gap-3">
    <div class="text-2xl font-semibold mb-4">
        {{ $planTitle }}
    </div>

    @php
        $plan = null;
        if ($plans->has($planType) && !$plans[$planType]->isEmpty()) {
            $plan = $plans[$planType][0];
        }
    @endphp
    @if ($plans->has($planType) && $plans[$planType]->count() > 1)
        <p class="text-red-400">
            Кол-во планов не должно быть больше 1!<br>
            Возможны ошибки в расчётах, удалите ненужные планы
        </p>
        @foreach ($plans[$planType] as $plan)
            <div class="flex gap-4 border-b-2 py-1 pb-3 w-full items-center">
                <form class="flex gap-4" method="POST"
                    action="{{ $plan == null ? route('workPlan.store') : route('workPlan.update', $plan->id) }}">
                    @csrf
                    @if ($plan)
                        @method('PUT')
                    @endif
                    <input type="hidden" name="type" value="{{ $planType }}">
                    <input type="hidden" name="department_id" value="{{ $departmentId }}">

                    @if ($hasGoalField)
                        <label class="flex gap-2 items-center whitespace-nowrap" for="goal">
                            Цель {{ isset($isPercentGoal) ? '(%)' : '' }}
                            <input {{ $isCurrentMonth ? '' : 'disabled' }} class="input" name="goal" type="number"
                                value="{{ $plan['goal'] ?? '' }}">
                        </label>
                    @endif

                    <label class="flex gap-2 items-center" for="bonus">
                        Бонус
                        <input {{ $isCurrentMonth ? '' : 'disabled' }} class="input" name="bonus" type="number"
                            value="{{ $plan['bonus'] ?? '' }}">
                    </label>

                    @if ($isCurrentMonth)
                        <button class="hover:text-blue-400">
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
    @else
        <form class="flex gap-4 border-b-2 py-1 pb-3" method="POST"
            action="{{ $plan == null ? route('workPlan.store') : route('workPlan.update', $plan->id) }}">
            @csrf
            @if ($plan)
                @method('PUT')
            @endif
            <input type="hidden" name="type" value="{{ $planType }}">
            <input type="hidden" name="department_id" value="{{ $departmentId }}">

            @if ($hasGoalField)
                <label class="flex gap-2 items-center whitespace-nowrap" for="goal">
                    Цель {{ isset($isPercentGoal) ? '(%)' : '' }}
                    <input {{ $isCurrentMonth ? '' : 'disabled' }} class="input" name="goal" type="number"
                        value="{{ $plan['goal'] ?? '' }}">
                </label>
            @endif

            <label class="flex gap-2 items-center" for="bonus">
                Бонус
                <input {{ $isCurrentMonth ? '' : 'disabled' }} class="input" name="bonus" type="number"
                    value="{{ $plan['bonus'] ?? '' }}">
            </label>

            @if ($isCurrentMonth)
                <button class="hover:text-blue-400">
                    Изменить
                </button>
            @endif
        </form>
    @endif
</div>
