<template>
    <tr class="bg-white border-b hover:bg-gray-50">
        <td class="px-6 py-4">
            <Link :href="route('admin.user.show', user.id)">
            {{ user.full_name }}
            </Link>
        </td>
        <td class="px-6 py-4 font-semibold" :class="getActionColor(user)">
            {{ translateAction(user.last_action?.action) || 'Не начал' }}
        </td>
        <td class="px-6 py-4 box-border min-h-16">
            <StatusEdit v-model:selectedStatusId="selectedStatusId" :statuses="updatedStatuses" :user="user"
                :date="date" :timeStart.sync="timeStart" :timeEnd.sync="timeEnd" :changeMode="changeMode"
                @openModal="openModal" @sendWorkStatus="sendWorkStatus" @toggleСhangeMode="toggleСhangeMode" />
        </td>

        <ModalPartTime v-if="modals['part_time']" :user="user" v-model:timeStart="timeStart" v-model:timeEnd="timeEnd"
            @save="sendWorkStatus" @close="closeModal" />

        <ModalSickLeave v-if="modals['sick_leave']" :user="user" v-model:dates="rangeDates"
            :workStatusId="selectedStatusId" @save="sendSickLeave" @close="closeModal" />

        <ModalCloseSickLeave v-if="modals['close_sick_leave']" v-model:dates="closeRangeDates" v-model:image="image"
            :user="user" @save="closeSickLeave" @close="closeModal" />
    </tr>
</template>

<script>
import StatusEdit from './StatusEdit.vue'
import ModalPartTime from './ModalPartTime.vue'
import ModalSickLeave from './ModalSickLeave.vue'
import ModalCloseSickLeave from './ModalCloseSickLeave.vue'
import { route } from 'ziggy-js'
import { router } from '@inertiajs/vue3'

export default {
    components: {
        StatusEdit,
        ModalPartTime,
        ModalSickLeave,
        ModalCloseSickLeave,
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
        async sendWorkStatus() {
            const statusObject = this.workStatuses.find(e => e.id === this.selectedStatusId)
            if (statusObject?.type === 'part_time_day' && (!this.timeStart || !this.timeEnd)) {
                alert('Пожалуйста, заполните время начала и конца рабочего дня.')
                return
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
        sendSickLeave() {

            router.post(route('admin.time-check.handle-sick-leave'),
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
        async closeSickLeave() {
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
        toggleСhangeMode(event) {
            this.changeMode = event
        },
        openModal(modal) {
            setTimeout(() => {
                this.modals[modal] = true
            }, 200)
        },
        closeModal() {
            this.modals.part_time = false
            this.modals.sick_leave = false
            this.modals.close_sick_leave = false
        },
    },
}
</script>