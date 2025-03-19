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
        <td class="px-6 py-4 box-border min-h-16">
            <div v-if="!changeMode" class="flex items-center justify-between gap-2">
                {{ selectedStatus }}
                <button @click="changeMode = true" class="p-1 text-gray-500 hover:text-gray-700 focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path
                            d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
                    </svg>
                </button>
            </div>
            <div v-else class="flex items-center justify-between gap-2">
                <VueSelect v-model="selectedStatusId" :options="updatedStatuses" :reduce="workStatuse => workStatuse.id"
                    label="name" class="full-vue-select max-h-16" @update:modelValue="updateStatus" />

                <button @click="changeMode = false" class="p-1 text-gray-500 hover:text-gray-700 focus:outline-none">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
        </td>
        <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
            <div class="bg-white rounded-lg p-6 w-1/3 max-h-[80vh] overflow-y-visible relative flex flex-col gap-3">
                <h2 class="text-xl font-bold mb-4">{{ user.full_name }} - Неполный рабочий день</h2>

                <div class="grid grid-cols-2 gap-3">
                    <div class="flex flex-col">
                        <label class="label">
                            Начало рабочего дня
                        </label>
                        <VueDatePicker v-model="timeStart" time-picker />
                    </div>
                    <div class="flex flex-col">
                        <label class="label">
                            Конец рабочего дня
                        </label>
                        <VueDatePicker v-model="timeEnd" time-picker />
                    </div>
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
import VueDatePicker from '@vuepic/vue-datepicker';
import '@vuepic/vue-datepicker/dist/main.css'

export default {
    components: {
        VueSelect,
        FormInput,
        VueDatePicker
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

        let selectedStatusId = null;
        let timeStart = null;
        let timeEnd = null;

        if (this.user.daily_work_statuses.length) {
            selectedStatusId = this.user.daily_work_statuses[0].work_status_id;
            timeStart = this.user.daily_work_statuses[0].time_start;
            timeEnd = this.user.daily_work_statuses[0].time_end;
        }

        return {
            selectedStatusId,
            updatedStatuses,
            isModalOpen: false,
            changeMode: false,
            timeStart,
            timeEnd,
        }
    },
    computed: {
        selectedStatus() {
            const status = this.updatedStatuses.find(status => status.id === this.selectedStatusId);
            return status ? status.name : 'Не проставлен статус';
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
            let selectedStatusId = this.selectedStatusId;
            let statusObject = this.workStatuses.find((element) => element.id == selectedStatusId)

            if (!statusObject) {
                this.sendWorkStatus()
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

            this.sendWorkStatus()

        },
        async sendWorkStatus() {
            const statusObject = this.workStatuses.find((element) => element.id == this.selectedStatusId);
            if (statusObject?.type === 'part_time_day') {
                if (!this.timeStart || !this.timeEnd) {
                    alert('Пожалуйста, заполните время начала и конца рабочего дня.');
                    return; 
                }
            }

            let data = {
                user_id: this.user.id,
                work_status_id: this.selectedStatusId,
                time_start: this.formatTime(this.timeStart),
                time_end: this.formatTime(this.timeEnd),
            }

            try {
                const response = await axios.post(
                    route('admin.time-check.handle-work-status'),
                    data,
                    { withCredentials: true }
                );
            } catch (error) {
                console.log(error.response.data);

                alert(error.response?.data?.error || 'Ошибка обновления статуса');
            } finally {
                this.changeMode = false;
            }
            this.closeModal();
        },
        formatTime(timeObject) {
            if (!timeObject) return null;

            const hours = String(timeObject.hours).padStart(2, '0');
            const minutes = String(timeObject.minutes).padStart(2, '0');

            return `${hours}:${minutes}`;
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