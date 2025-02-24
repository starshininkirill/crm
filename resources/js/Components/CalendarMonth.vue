<template>
    <div class="bg-gray-800 rounded shadow h-fit border border-gray-200">
        <table class="w-full text-center text-white border-collapse">
            <thead>
                <tr>
                    <th colspan="7" class="py-2 font-semibold bg-gray-800 border-b border-gray-600">
                        {{ monthName }}
                    </th>
                </tr>
                <tr class="bg-gray-800 font-semibold border-white border-t-2 border-b-2">
                    <th class="w-12 h-12 border border-gray-200">Пн</th>
                    <th class="w-12 h-12 border border-gray-200">Вт</th>
                    <th class="w-12 h-12 border border-gray-200">Ср</th>
                    <th class="w-12 h-12 border border-gray-200">Чт</th>
                    <th class="w-12 h-12 border border-gray-200">Пт</th>
                    <th class="w-12 h-12 border border-gray-200">Сб</th>
                    <th class="w-12 h-12 border border-gray-200">Вс</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="week in weeks" :key="week">
                    <td v-for="day in week" :key="day ? day.date : Math.random()" @click="day && toggleType(day)"
                        :class="day ? [
                            'w-12', 'h-12', 'border', 'border-gray-200', 'cursor-pointer',
                            day.is_workday ? 'bg-white text-black' : 'bg-red-500 text-white'
                        ] : 'bg-white w-12 h-12 border border-gray-200'">
                        {{ day ? formatDate(day.date) : '' }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
import axios from 'axios';
import { route } from 'ziggy-js';

export default {
    props: {
        monthName: String,
        weeks: Array,
    },
    methods: {
        async toggleType(day) {
            let formatedDate = new Date(day.date).toISOString().slice(0, 10);
            try {
                const response = await axios.post( route('admin.settings.calendar.change-day'), {
                    date: formatedDate,
                    is_working_day: day.is_workday,
                },
                    { withCredentials: true });
                day.is_workday = response.data.is_working_day;
                this.$forceUpdate();
            } catch (error) {
                alert("Ошибка при обновлении рабочего дня: " + error.response.data.error);
            }
        },
        formatDate(dateString) {
            const date = new Date(dateString);
            const day = date.getDate().toString().padStart(2, '0');
            return `${day}`;
        }
    },
};
</script>
