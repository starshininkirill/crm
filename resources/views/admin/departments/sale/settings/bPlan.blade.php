<div class="flex flex-col gap-3">
    <div class=" text-2xl font-semibold mb-4">
        {{ $title }}
    </div>

    @if (!$plans->has($planType) || $plans[$planType]->isEmpty())
        <div class=" text-xl mb-4">
            Нет планов
        </div>
    @else
        <div class="grid grid-cols-2 gap-4">
            @foreach ($plans[$planType]->sortBy('service_category_id') as $plan)
                <div class="flex flex-col gap-4 border-b-2 py-1 pb-3 w-full items-start">
                    <div class="top">
                        {{ $plan->serviceCategory->name }}
                    </div>
                    <div class="flex gap-2 items-end">
                        <form class="flex flex-col gap-2" method="POST"
                            action="{{ $plan == null ? route('workPlan.store') : route('workPlan.update', $plan->id) }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="type" value="{{ $planType }}">
                            <input type="hidden" name="bonus" value="{{ $plan['bonus'] }}">
                            <div class="flex gap-2 mt-auto items-end">
                                <label class="flex flex-col gap-2" for="goal">
                                    <input {{ $isCurrentMonth ? '' : 'disabled' }} class="input" name="goal"
                                        type="number" value="{{ $plan['goal'] ?? '' }}">
                                </label>
                                @if ($isCurrentMonth)
                                    <button class=" hover:text-blue-400">
                                        Изменить
                                    </button>
                                @endif
                            </div>
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

                </div>
            @endforeach
        </div>
        <form class="flex items-end gap-4 border-b-2 py-1 pb-3" method="POST"
            action="{{ $plan == null ? route('workPlan.store') : route('workPlan.update', $plan->id) }}">
            @php
                $plan = $plans[$planType][0];
            @endphp
            @csrf
            @method('PUT')
            <input type="hidden" name="type" value="{{ $planType }}">
            <input type="hidden" name="department_id" value="{{ $departmentId }}">
            <input name="goal" type="hidden" value="{{ $plan['goal'] }}">
            <label class="flex flex-col gap-2 w-full" for="bonus">
                Бонус (%)
                <input {{ $isCurrentMonth ? '' : 'disabled' }} class="input" name="bonus" type="number"
                    value="{{ $plan['bonus'] ?? '' }}">
            </label>
            @if ($isCurrentMonth)
                <button class=" hover:text-blue-400">
                    Изменить
                </button>
            @endif
        </form>
    @endif


    @if ($isCurrentMonth)
        @if ($plans->has($planType) && !$plans[$planType]->isEmpty())
            @php
                $usedCategoryIds = $plans[$planType]->pluck('service_category_id');
                $categoriesForCalculations = $categoriesForCalculations->whereNotIn('id', $usedCategoryIds);
            @endphp
        @endif

        @if (!$categoriesForCalculations->isEmpty())
            <div class=" text-xl font-semibold">
                Создать план
            </div>
            <form class="flex flex-col gap-2" method="POST" action="{{ route('workPlan.store') }}">
                @csrf
                <input type="hidden" name="type" value="{{ $planType }}">
                <input type="hidden" name="department_id" value="{{ $departmentId }}">
                <input type="hidden" name="created_at" value="{{ $date->format('Y-m-d') }}">
                <div class="grid grid-cols-2 gap-3">
                    @if ($plans->has($planType) && !$plans[$planType]->isEmpty())
                        <input type="hidden" name="bonus" value="{{ $plans[$planType][0]->bonus }}">
                    @else
                        <label class="flex flex-col gap-2" for="bonus">
                            Бонус (%)
                            <input class="input" name="bonus" type="number">
                        </label>
                    @endif
                    <label class="flex flex-col gap-2" for="goal">
                        Цель
                        <input class="input" name="goal" type="number">
                    </label>
                </div>
                <select class="select" name="service_category_id" id="">
                    @foreach ($categoriesForCalculations as $category)
                        <option value="{{ $category->id }}">
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
                <button class="btn">
                    Создать
                </button>
            </form>
        @endif
    @endif
</div>
