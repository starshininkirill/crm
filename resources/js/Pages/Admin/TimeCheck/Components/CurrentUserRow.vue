<template>
    <tr class="bg-white border-b hover:bg-gray-50 ">
        <td class="px-6 py-4">
            <Link :href="route('admin.user.show', user.id)">
            {{ user.full_name }}
            </Link>
        </td>
        <td class="px-6 py-4 font-semibold" :class="getActionColor(user)">
            {{ translateAction(user.last_action?.action) || 'Не начал' }}
        </td>
        <td class="px-6 py-4 box-border">
            <VueSelect v-model="selectedStatus" :options="updatedStatuses" :reduce="workStatuse => workStatuse.id"
                label="name" class="full-vue-select" @update:modelValue="updateStatus" />

        </td>
        <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg p-6 w-1/3 max-h-[80vh] overflow-y-auto relative flex flex-col gap-3">
                <h2 class="text-xl font-bold mb-4">{{ user.full_name }} - Неполный рабочий день</h2>

                <div class="grid grid-cols-2 gap-3">
                    <FormInput v-model="timeStart" type="time" name="time_start" label="Время начала" />

                    <FormInput v-model="timeEnd" type="time" name="time_end" label="Время конца" />

                </div>

                <div class="btn" @click="sendWorkStatus">
                    Сохранить
                </div>

                <button @click="closeModal"
                    class=" w-6 h-6 bg-red-500 text-white rounded hover:bg-red-600 absolute right-4 top-4">
                    x
                </button>
            </div>
        </div>
    </tr>
</template>

<script>
import VueSelect from 'vue-select';
import FormInput from '../../../../Components/FormInput.vue';
import axios from 'axios';
import { route } from 'ziggy-js';

export default {
    components: {
        VueSelect,
        FormInput
    },
    props: {
        user: {
            type: Object,
            required: true,
        },
        workStatuses: {
            type: Array,
            required: true,
        },
    },
    data() {
        const updatedStatuses = [
            { name: 'Не проставлен статус', id: null },
            ...this.workStatuses,
        ];

        let selectedStatus = null;
        let timeStart = null;
        let timeEnd = null;

        if (this.user.daily_work_statuses.length) {
            selectedStatus = this.user.daily_work_statuses[0].work_status_id;
            timeStart = this.user.daily_work_statuses[0].time_start;
            timeEnd = this.user.daily_work_statuses[0].time_end;
        }

        return {
            selectedStatus,
            updatedStatuses,
            isModalOpen: false,
            timeStart,
            timeEnd,
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
        },
        updateStatus() {
            let selectedStatus = this.selectedStatus;
            let statusObject = this.workStatuses.find((element) => element.id == selectedStatus)

            if (!statusObject) {
                // Удалить статус за день
                return;
            }

            if (statusObject.type == 'part_time_day') {
                this.openModal()
                return;
            }

            if (statusObject.type != 'part_time_day') {
                this.timeStart = null;
                this.timeEnd = null;
            }

            if (this.selectedStatus != null) {
                this.sendWorkStatus()
            }
        },
        async sendWorkStatus() {
            let data = {
                user_id: this.user.id,
                work_status_id: this.selectedStatus,
                time_start: this.timeStart,
                time_end: this.timeEnd,
            }

            try {
                const response = await axios.post(
                    route('admin.time-check.handle-work-status'),
                    data,
                    { withCredentials: true }
                );

                alert('Статус успешно обновлён!')

            } catch (error) {
                console.log(error.response.data);

                alert(error.response?.data?.error || 'Ошибка обновления статуса');
            }
            this.closeModal();
        },
        openModal() {
            let th = this
            setTimeout(function () {
                th.isModalOpen = true;
            }, 200)
        },
        closeModal() {
            this.isModalOpen = false;
        },
    }
}
</script>