<div class="flex flex-col gap-3">
    <div class=" text-2xl font-semibold mb-4">
        Двойной план
    </div>

    @php
        $plan = null;
        if ($plans->has($workPlanClass::DOUBLE_PLAN) && !$plans[$workPlanClass::DOUBLE_PLAN]->isEmpty()) {
            $plan = $plans[$workPlanClass::DOUBLE_PLAN][0];
        }
    @endphp
    <form class="flex gap-4 border-b-2 py-1 pb-3" method="POST"
        action="{{ $plan == null ? route('workPlan.store') : route('workPlan.update', $plan->id) }}">
        @csrf
        @if ($plan)
            @method('PUT')
        @endif
        <input type="hidden" name="type" value="{{ $workPlanClass::DOUBLE_PLAN }}">
        <input type="hidden" name="department_id" value="{{ $departmentId }}">
        <label class="flex gap-2 items-center" for="bonus">
            Бонус
            <input {{ $isCurrentMonth ? '' : 'disabled' }} class="input" name="bonus" type="number"
                value="{{ $plan['bonus'] ?? '' }}">
        </label>
        @if ($isCurrentMonth)
            <button class=" hover:text-blue-400">
                Изменить
            </button>
        @endif
    </form>
</div>
