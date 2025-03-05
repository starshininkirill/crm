<template>
    <tr class="bg-white border-b dark:bg-gray-800 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600">
        <td class="px-6 py-4">
            <Link :href="route('admin.user.show', user.id)">
            {{ user.full_name }}
            </Link>
        </td>
        <td class="px-6 py-4 font-semibold" :class="getActionColor(user)">
            {{ translateAction(user.last_action?.action) || 'Не начал'}}
        </td>
        <td class="px-6 py-4">
            <button>Изменить статус</button>
        </td>
    </tr>
</template>

<script>
export default {
    props: {
        user: {
            type: Object,
            required: true,
        }
    },
    methods: {
        getActionColor(user) {
            if (user.last_action == null) {
                return '';
            }
            let action = user.last_action.action
            if (action === 'start' || action === 'continue') {
                return 'text-green-600';
            } else if (action === 'pause') {
                return 'text-yellow-600';
            } else if (action === 'end') {
                return 'text-red-600';
            } else {
                return '';
            }
        },
        translateAction(action) {
            let translations = {
                'start': 'Работает',
                'end': 'Закончил',
                'pause': 'На перерыве',
                'continue': 'Работает',
            };
            return translations[action];
        }
    }
}
</script>