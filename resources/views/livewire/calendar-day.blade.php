<td wire:click="toggleType"
    class="w-12 h-12 border border-gray-200 cursor-pointer
            {{ $day['is_workday'] ? 'bg-white text-black' : 'bg-red-500 text-white' }}">
    {{ $day['date']->day }}
</td>
