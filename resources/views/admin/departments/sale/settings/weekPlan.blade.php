<div class="flex flex-col gap-3">
    <div class=" text-2xl font-semibold mb-4">
        План недели
    </div>

    @php
        $plan = null;
        if ($plans->has($workPlanClass::WEEK_PLAN) && !$plans[$workPlanClass::WEEK_PLAN]->isEmpty()) {
            $plan = $plans[$workPlanClass::WEEK_PLAN][0];
        }
    @endphp
    <form class="flex gap-4 border-b-2 py-1 pb-3" method="POST"
        action="{{ $plan == null ? route('workPlan.store') : route('workPlan.update', $plan->id) }}">
        @csrf
        @if ($plan)
            @method('PUT')
        @else
            <input type="hidden" name="department_id" value="{{ $departmentId }}">
        @endif
        <input type="hidden" name="type" value="{{ $workPlanClass::WEEK_PLAN }}">
        <label class="flex gap-2 items-center" for="bonus">
            Бонус
            <input class="input" name="bonus" type="number" value="{{ $plan['bonus'] ?? '' }}">
        </label>
        <button class=" hover:text-blue-400">
            Изменить
        </button>
    </form>
</div>
