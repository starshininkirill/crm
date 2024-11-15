<div class="flex flex-col gap-2 w-fit">
    <div class=" text-2xl font-semibold mb-4">
        Процентная лестница
    </div>
    <div class="flex flex-col gap-2 mb-4">
        @if (!$plans->has($workPlanClass::PERCENT_LADDER) || $plans[$workPlanClass::PERCENT_LADDER]->isEmpty())
            <div class=" text-xl mb-4">
                Нет данных
            </div>
        @else
            @foreach ($plans[$workPlanClass::PERCENT_LADDER] as $plan)
                <div class="plan flex gap-4 border-b-2 py-1 pb-3 w-full items-center">
                    <form class="flex gap-4" method="POST" action="{{ route('workPlan.update', $plan->id) }}">
                        @method('PUT')
                        @csrf
                        <input type="hidden" name="type" value="{{ $workPlanClass::PERCENT_LADDER }}">
                        <input type="hidden" name="department_id" value="{{ $departmentId }}">
                        <label class="flex gap-2 items-center" for="goal">
                            До
                            <input {{ $isCurrentMonth ? '' : 'disabled' }} class="input" name="goal" type="number"
                                value="{{ $plan['goal'] }}">
                        </label>
                        <label class="flex gap-2 items-center" for="bonus">
                            %
                            <input {{ $isCurrentMonth ? '' : 'disabled' }} class="input" name="bonus" type="number"
                                step="0.1" value="{{ $plan['bonus'] }}">
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
        <form class="mb-4" method="POST" action="{{ route('workPlan.store') }}">
            @csrf
            <div class=" text-xl mb-2">
                Новая цель
            </div>
            <div class="flex flex-col gap-2">
                <input type="hidden" name="type" value="{{ $workPlanClass::PERCENT_LADDER }}">
                <input type="hidden" name="department_id" value="{{ $departmentId }}">
                <label class="flex gap-2 items-center" for="goal">
                    До
                    <input class="input" name="goal" type="number">
                </label>
                <label class="flex gap-2 items-center" for="bonus">
                    %
                    <input class="input" name="bonus" type="number" step="0.1">
                </label>
                <button class="btn">
                    Создать
                </button>
            </div>
        </form>
    @endif
    @if ($plans->has($workPlanClass::NO_PERCENTAGE_MONTH) && !$plans[$workPlanClass::NO_PERCENTAGE_MONTH]->isEmpty())
        @php
            $monthOption = $plans[$workPlanClass::NO_PERCENTAGE_MONTH]->first();
        @endphp
    @else
        @php
            $monthOption = null;
        @endphp
    @endif
    <form method="POST"
        action="{{ $monthOption == null ? route('workPlan.store') : route('workPlan.update', $monthOption->id) }}">
        @csrf
        <input type="hidden" name="type" value="{{ $workPlanClass::NO_PERCENTAGE_MONTH }}">
        <input type="hidden" name="department_id" value="{{ $departmentId }}">
        <div class=" text-xl mb-2">
            До какого месяца всегда начисляются проценты
        </div>
        <div class="flex flex-col gap-2">
            @if ($monthOption)
                @method('PUT')
            @endif
            <input type="hidden" name="name" month="sale_department_ladder_month">
            <label class="flex gap-2 items-center" for="month">
                <input {{ $isCurrentMonth ? '' : 'disabled' }} class="input" name="month" type="number" step="1" min="0"
                    value="{{ $monthOption != null ? $monthOption['month'] : '' }}">
            </label>
            @if ($isCurrentMonth)
                <button class="btn">
                    @if ($monthOption)
                        Изменить
                    @else
                        Создать
                    @endif
                </button>
            @endif
        </div>
    </form>
</div>
