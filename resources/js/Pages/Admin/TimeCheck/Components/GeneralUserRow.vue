<template>
    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
        <td class="px-6 py-4">
            <Link :href="route('admin.user.show', user.id)">
            {{ user.full_name }}
            </Link>
        </td>
        <td class="px-6 py-4">
            {{ date }}
        </td>
        <td class="px-6 py-4">
            <div v-if="user.actionStart">
                {{ user.actionStart }}
            </div>
            <div v-else class="font-semibold text-red-500">
                Рабочий день не начат
            </div>
        </td>
        <td class="px-6 py-4">
            <div v-if="user.actionEnd">
                {{ user.actionEnd }}
            </div>
            <div v-else>
                Рабочий день не завершён
            </div>
        </td>
        <td class="px-6 py-4">
            {{ formatTime(user.workTime) }}
        </td>
        <td class="px-6 py-4">
            {{ formatTime(user.breaktime) }}
        </td>
    </tr>
</template>
<script>
export default {
    props: {
        user: {
            type: Object,
            required: true,
        },
        date: {
            type: String,
            required: true,
        }
    },
    methods: {
        formatTime(seconds) {
            if (seconds == 0) {
                return '00:00:00';
            } else {
                return new Date(seconds * 1000).toISOString().substr(11, 8);
            }
        },
    },
}
</script>