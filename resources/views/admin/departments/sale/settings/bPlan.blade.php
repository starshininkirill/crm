<div class="flex flex-col gap-3">
    <div class=" text-2xl font-semibold mb-4">
        {{ $title }}
    </div>

    @if (!$plans->has($planType) || $plans[$planType]->isEmpty())
        <div class=" text-xl mb-4">
            Нет планов
        </div>
        @if (!$categoriesForCalculations->isEmpty())
            <form method="POST" action="">
                <input type="hidden" name="type" value="{{ $planType }}">
                <input type="hidden" name="department_id" value="{{ $departmentId }}">
                <label class="flex flex-col gap-2" for="goal">
                    Цель
                    <input class="input" name="goal" type="number" value="{{ $plan['goal'] ?? '' }}">
                </label>
                <select class="select" name="department_id" id="">
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
    @else
        <div class="grid grid-cols-2 gap-2">
            @foreach ($plans[$planType]->sortBy('service_category_id') as $plan)
                <form class="flex flex-col gap-4 border-b-2 py-1 pb-3" method="POST"
                    action="{{ $plan == null ? route('workPlan.store') : route('workPlan.update', $plan->id) }}">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="type" value="{{ $planType }}">
                    <input type="hidden" name="department_id" value="{{ $departmentId }}">
                    <input type="hidden" name="bonus" value="{{ $plan['bonus'] }}">
                    {{ $plan->serviceCategory->name }}
                    <div class="flex gap-2 mt-auto items-end">
                        <label class="flex flex-col gap-2" for="goal">
                            <input class="input" name="goal" type="number" value="{{ $plan['goal'] ?? '' }}">
                        </label>
                        <button class=" hover:text-blue-400">
                            Изменить
                        </button>
                    </div>
                </form>
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
                <input class="input" name="bonus" type="number" value="{{ $plan['bonus'] ?? '' }}">
            </label>
            <button class=" hover:text-blue-400">
                Изменить
            </button>
        </form>
    @endif
</div>