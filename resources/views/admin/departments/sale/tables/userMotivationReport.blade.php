<div class="flex flex-col">
    <table class="min-w-full border border-gray-300">
        <thead>
            <tr class="bg-gray-800">
                <th style="width: calc(100% / {{ $motivationReport['weeksPlan']->count() + 1 }})" class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">План недели
                </th>
                @foreach ($motivationReport['weeksPlan'] as $key => $week)
                    <th style="width: calc(100% / {{ $motivationReport['weeksPlan']->count() + 1 }})" class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">
                        неделя {{ $key + 1 }}
                    </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ number_format($week['goal'], 0, ' ', ' ') }} ₽
                </td>
                @foreach ($motivationReport['weeksPlan'] as $week)
                    <td class="border text-xs border-gray-300 text-md px-1 py-1">
                        @if ($week['completed'])
                            Да
                        @else
                            Нет
                        @endif
                    </td>
                @endforeach
            </tr>
        </tbody>
    </table>
    <table class="min-w-full border border-gray-300">
        <thead>
            <tr class="bg-gray-800">
                <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white" style="width: 12.5%">План месяца
                </th>
                <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white" style="width: 12.5%">Двойной план
                </th>
                <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white" style="width: 12.5%">Бонус план
                </th>
                <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white" style="width: 12.5%">Супер план
                </th>
                <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white" style="width: 12.5%">Б1</th>
                <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white" style="width: 12.5%">Б2</th>
                <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white" style="width: 12.5%">Б3</th>
                <th class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white" style="width: 12.5%">Б4</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ number_format($motivationReport['mounthPlan']['goal'], 0, ' ', ' ') }} ₽
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ $motivationReport['doublePlan']['completed'] ? 'Да' : 'Нет' }}
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ $motivationReport['bonusPlan']['completed'] ? 'Да' : 'Нет' }}
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ $motivationReport['superPlan']['completed'] ? 'Да' : 'Нет' }}
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ $motivationReport['b1']['completed'] ? 'Да' : 'Нет' }}
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ $motivationReport['b2']['completed'] ? 'Да' : 'Нет' }}
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ $motivationReport['b3']['completed'] ? 'Да' : 'Нет' }}
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ $motivationReport['b4']['completed'] ? 'Да' : 'Нет' }}
                </td>
            </tr>
        </tbody>
    </table>
    <table class="min-w-full border border-gray-300">
        <thead>
            <tr class="bg-gray-800">
                <th style="width: calc(100% / 5)" class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">
                    %
                </th>
                <th style="width: calc(100% / 5)" class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">
                    % От Новых
                </th>
                <th style="width: calc(100% / 5)" class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">
                    % От Старых
                </th>
                <th style="width: calc(100% / 5)" class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">
                    Бонусы
                </th>
                <th style="width: calc(100% / 5)" class="border text-xs border-gray-300 text-md px-1 py-1 text-left text-white">
                    Итого
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ $motivationReport['salary']['percentage'] }} %
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ number_format($motivationReport['salary']['newMoney'], 0, ' ', ' ') }} ₽
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ number_format($motivationReport['salary']['oldMoney'], 0, ' ', ' ') }} ₽
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ number_format($motivationReport['salary']['bonuses'], 0, ' ', ' ') }} ₽
                </td>
                <td class="border text-xs border-gray-300 text-md px-1 py-1">
                    {{ number_format($motivationReport['salary']['amount'], 0, ' ', ' ') }} ₽
                </td>
            </tr>
        </tbody>
    </table>
</div>
