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
                            <input class="input" name="goal" type="number" value="{{ $plan['goal'] }}">
                        </label>
                        <label class="flex gap-2 items-center" for="bonus">
                            %
                            <input class="input" name="bonus" type="number" step="0.1"
                                value="{{ $plan['bonus'] }}">
                        </label>
                        <button class=" hover:text-blue-400">
                            Изменить
                        </button>
                    </form>
                    <form method="POST" action="{{ route('workPlan.destroy', $plan->id) }}">
                        @method('DELETE')
                        @csrf
                        <button class="hover:text-red-500">
                            Удалить
                        </button>
                    </form>
                </div>
            @endforeach
        @endif
    </div>
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
                <input class="input" name="goal" type="number"">
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
    <form method="POST" action="{{ $mounthOption == null ? route('option.store') : route('option.update', $mounthOption->id) }}">
        @csrf
        <div class=" text-xl mb-2">
            До какого месяца всегда начисляются проценты
        </div>
        <div class="flex flex-col gap-2">
            @if ($mounthOption)
                @method('PUT')
            @endif
            <input type="hidden" name="name" value="sale_department_ladder_mounth">
            <label class="flex gap-2 items-center" for="value">
                <input class="input" name="value" type="number" step="1" min="0"
                    value="{{ $mounthOption != null ? $mounthOption['value'] : '' }}">
            </label>
            <button class="btn">
                @if ($mounthOption)
                    Изменить
                @else
                    Создать
                @endif
            </button>
        </div>
    </form>
</div>