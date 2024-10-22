<table class="min-w-full border border-gray-300">
    <thead>
        <tr class="bg-gray-800">
            <th class="border text-xs border-gray-300 text-md px-2 py-1 text-left text-white">Дата</th>
            <th class="border text-xs border-gray-300 text-md px-2 py-1 text-left text-white">NEW $</th>
            <th class="border text-xs border-gray-300 text-md px-2 py-1 text-left text-white">OLD $</th>
            <th class="border text-xs border-gray-300 text-md px-2 py-1 text-left text-white">Инд</th>
            <th class="border text-xs border-gray-300 text-md px-2 py-1 text-left text-white">Гот</th>
            <th class="border text-xs border-gray-300 text-md px-2 py-1 text-left text-white">РК</th>
            <th class="border text-xs border-gray-300 text-md px-2 py-1 text-left text-white">SEO</th>
            <th class="border text-xs border-gray-300 text-md px-2 py-1 text-left text-white">Допы</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($daylyReport as $data)
            <tr>
                <td class="border text-xs border-gray-300 text-md px-2 py-1">
                    {{ \Carbon\Carbon::parse($data['date'])->format('d.m.y') }}</td>
                <td class="border text-xs border-gray-300 text-md px-2 py-1">
                    {{ number_format($data['newMoney'], 0, ' ', ' ') }} ₽</td>
                <td class="border text-xs border-gray-300 text-md px-2 py-1">
                    {{ number_format($data['oldMoney'], 0, ' ', ' ') }} ₽</td>
                <td class="border text-xs border-gray-300 text-md px-2 py-1">
                    {{ $data['individualSites'] }}
                </td>
                <td class="border text-xs border-gray-300 text-md px-2 py-1">
                    {{ $data['readiesSites'] }}
                </td>
                <td class="border text-xs border-gray-300 text-md px-2 py-1">{{ $data['rk'] }}</td>
                <td class="border text-xs border-gray-300 text-md px-2 py-1">{{ $data['seo'] }}</td>
                <td class="border text-xs border-gray-300 text-md px-2 py-1">{{ $data['other'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>