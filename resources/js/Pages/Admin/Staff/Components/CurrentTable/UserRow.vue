<template>
    <tr class="table-row">
        <td class="px-2 py-2 border-x">
            <Link :href="route('admin.user.show', user.id)">
            {{ user.full_name }}
            </Link>
        </td>
        <td class="px-2 py-2 border-r font-semibold whitespace-nowrap w-32" :class="getActionColor(user)">
            {{ translateAction(user.last_action?.action) || 'Не начал' }}
        </td>
        <td class="px-2 py-2 border-r">
            <div v-if="user.actionStart" class="flex gap-1 items-center justify-between"
                :class="user.isLate ? 'font-semibold text-red-500' : ''">
                {{ user.actionStart }}
                <button title="Отменить опоздание" v-if="user.isLate" @click="rejectLate"
                    class="p-1 text-gray-500 hover:text-gray-700">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                            d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                            clip-rule="evenodd" />
                    </svg>
                </button>
            </div>
            <div v-else class="font-semibold text-red-500">
                Не начат
            </div>
        </td>
        <td class="px-2 py-2 border-r">
            <div v-if="user.actionEnd">
                {{ user.actionEnd }}
            </div>
            <div v-else>
                Не завершён
            </div>
        </td>
        <td class="px-2 py-2 border-r">
            {{ formatWorkTime(user.workTime) }}
        </td>
        <td class="px-2 py-2 border-r" :class="user.isOvertime ? 'font-semibold text-red-500' : ''">
            {{ formatWorkTime(user.breaktime) }}
        </td>
        <td class="px-2 py-2 border-x box-border min-h-16">
            <StatusEdit v-model:selectedStatusId="selectedStatusId" :statuses="updatedStatuses" :user="user"
                :date="date" :timeStart.sync="timeStart" :timeEnd.sync="timeEnd" :changeMode="changeMode"
                @openModal="openModal" @sendWorkStatus="sendWorkStatus" @toggleСhangeMode="toggleСhangeMode" />
        </td>

        <Modal :open="modals['part_time']" @close="closeModal">
            <PartTimeForm :user="user" v-model:timeStart="timeStart" v-model:timeEnd="timeEnd" @save="sendWorkStatus"
                @close="closeModal" />
        </Modal>

        <Modal :open="modals['sick_leave']" @close="closeModal">
            <SickLeaveForm :user="user" v-model:dates="rangeDates" :workStatusId="selectedStatusId"
                @save="sendMassUpdate" @close="closeModal" />
        </Modal>

        <Modal :open="modals['vacation']" @close="closeModal">
            <VacationForm :user="user" v-model:dates="rangeDates" :workStatusId="selectedStatusId" @save="sendMassUpdate"
                @close="closeModal" />
        </Modal>

        <Modal :open="modals['close_sick_leave']" @close="closeModal">
            <CloseSickLeaveForm v-model:dates="closeRangeDates" v-model:image="image" :user="user"
                @save="closeSickLeave" @close="closeModal" />
        </Modal>
    </tr>
</template>

<script>
import StatusEdit from './StatusEdit.vue'
import PartTimeForm from './PartTimeForm.vue'
import SickLeaveForm from './SickLeaveForm.vue'
import CloseSickLeaveForm from './CloseSickLeaveForm.vue'
import VacationForm from './VacationForm .vue'
import { route } from 'ziggy-js'
import { router } from '@inertiajs/vue3'
import Modal from '../../../../../Components/Modal.vue'

export default {
    components: {
        StatusEdit,
        PartTimeForm,
        SickLeaveForm,
        CloseSickLeaveForm,
        VacationForm,
        Modal
    },
    props: {
        user: Object,
        workStatuses: Array,
        date: String,
    },
    data() {
        const updatedStatuses = [{ name: 'Не проставлен статус', id: null }, ...this.workStatuses]
        let selectedStatusId = null
        let timeStart = null
        let timeEnd = null

        if (this.user.daily_work_statuses.length) {
            selectedStatusId = this.user.daily_work_statuses[0].work_status_id
            timeStart = this.user.daily_work_statuses[0].time_start
            timeEnd = this.user.daily_work_statuses[0].time_end
        }

        return {
            updatedStatuses,
            selectedStatusId,
            timeStart,
            timeEnd,
            changeMode: false,
            rangeDates: [],
            closeRangeDates: [],
            image: null,
            modals: {
                vacation: false,
                part_time: false,
                sick_leave: false,
                close_sick_leave: false,
            },
        }
    },
    computed: {
        selectedStatus() {
            const status = this.updatedStatuses.find(status => status.id === this.selectedStatusId)
            return status ? status.name : 'Не проставлен статус'
        },
    },
    methods: {
        formatWorkTime(seconds) {
            if (seconds == 0) {
                return '00:00:00';
            } else {
                return new Date(seconds * 1000).toISOString().substr(11, 8);
            }
        },
        getActionColor(user) {
            const action = user?.last_action?.action
            return {
                start: 'text-green-600',
                continue: 'text-green-600',
                pause: 'text-yellow-600',
                end: 'text-red-600',
            }[action] || ''
        },
        translateAction(action) {
            return {
                start: 'Работает',
                continue: 'Работает',
                pause: 'На перерыве',
                end: 'Закончил',
            }[action]
        },
        formatTime(timeObject) {
            if (!timeObject) return null
            return `${String(timeObject.hours).padStart(2, '0')}:${String(timeObject.minutes).padStart(2, '0')}`
        },
        sendWorkStatus() {
            const statusObject = this.workStatuses.find(e => e.id === this.selectedStatusId)
            if (statusObject?.type === 'part_time_day' && (!this.timeStart || !this.timeEnd)) {
                alert('Пожалуйста, заполните время начала и конца рабочего дня.')
                return
            }

            if (statusObject?.type != 'part_time_day') {
                this.timeStart = null;
                this.timeEnd = null;
            }

            router.post(route('admin.time-check.handle-work-status'),
                {
                    user_id: this.user.id,
                    date: this.date,
                    work_status_id: this.selectedStatusId,
                    time_start: this.formatTime(this.timeStart),
                    time_end: this.formatTime(this.timeEnd),
                },
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        this.changeMode = false;
                        this.closeModal()
                    },
                },
            )
        },
        sendMassUpdate() {

            router.post(route('admin.time-check.handle-mass-update'),
                {
                    user_id: this.user.id,
                    dates: this.rangeDates,
                    work_status_id: this.selectedStatusId,
                },
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        this.rangeDates = []
                        this.changeMode = false;
                        this.closeModal()
                    },
                },
            )

        },
        closeSickLeave() {
            var formData = {
                user_id: this.user.id,
                dates: this.closeRangeDates,
                image: this.image,
            }

            router.post(route('admin.time-check.close-sick-leave'),
                formData,
                {
                    preserveScroll: true,
                    onSuccess: () => {
                        this.closeModal();
                    },
                },
            )
        },
        rejectLate() {
            let formData = {
                user_id: this.user.id,
                date: this.date,
            }
            router.post(route('admin.time-check.reject-late'),
                formData,
                {
                    preserveScroll: true,
                },
            )
        },
        toggleСhangeMode(event) {
            this.changeMode = event
        },
        openModal(modal) {
            setTimeout(() => {
                this.modals[modal] = true
            }, 200)
        },
        closeModal() {
            this.modals.vacation = false
            this.modals.part_time = false
            this.modals.sick_leave = false
            this.modals.close_sick_leave = false
        },
    },
}
</script>