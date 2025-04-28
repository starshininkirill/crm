<template>
  <div v-if="!changeMode" class="flex items-center justify-between gap-2">
    <div class="flex gap-1 items-center"
      :class="sickLeaveDailyStatus?.status == 'approved' ? ' text-green-500 font-semibold' : ''">
      {{ selectedStatus?.name }}
    </div>
    <button v-if="needCloseSeakLeave" @click="$emit('openModal', 'close_sick_leave')" title="Закрыть больничный"
      class="p-1 text-gray-500 hover:text-gray-700 ml-auto">
      <svg class="w-6 h-6" xmlns="http://www.w3.org/2000/svg" version="1.1" xmlns:xlink="http://www.w3.org/1999/xlink"
        width="512" height="512" x="0" y="0" viewBox="0 0 64 64" style="enable-background:new 0 0 512 512"
        xml:space="preserve">
        <g>
          <path
            d="M35 24h-6a1 1 0 0 0-1 1v5h-5a1 1 0 0 0-1 1v6a1 1 0 0 0 1 1h5v5a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1v-5h5a1 1 0 0 0 1-1v-6a1 1 0 0 0-1-1h-5v-5a1 1 0 0 0-1-1zm5 8v4h-5a1 1 0 0 0-1 1v5h-4v-5a1 1 0 0 0-1-1h-5v-4h5a1 1 0 0 0 1-1v-5h4v5a1 1 0 0 0 1 1zM12 31a1 1 0 0 0-1 1v28a1 1 0 0 0 1 1h29a1 1 0 0 0 0-2H13V32a1 1 0 0 0-1-1zM53 20a1 1 0 0 0-2 0v39h-4a1 1 0 0 0 0 2h5a1 1 0 0 0 1-1zM52.923 13.618a1 1 0 0 0-.217-.326l-9.998-9.998A1.013 1.013 0 0 0 42 3H12a1 1 0 0 0-1 1v22a1 1 0 0 0 2 0V5h28v9a1 1 0 0 0 1 1h10a1 1 0 0 0 .923-1.382zM43 6.414 49.586 13H43z"
            fill="#000000" opacity="1" data-original="#000000"></path>
        </g>
      </svg>
    </button>
    <button title="Редактировать" @click="$emit('toggleСhangeMode', true)"
      class="p-1 text-gray-500 hover:text-gray-700">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path
          d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z" />
      </svg>
    </button>
  </div>

  <div v-else class="flex items-center justify-between gap-2">
    <VueSelect v-model="innerSelectedId" :options="statuses" :reduce="s => s.id" label="name"
      class="full-vue-select max-h-16" @update:modelValue="handleChange" />
    <button title="Отменить" @click="$emit('toggleСhangeMode', false)" class="p-1 text-gray-500 hover:text-gray-700">
      <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
        <path fill-rule="evenodd"
          d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
          clip-rule="evenodd" />
      </svg>
    </button>
  </div>
</template>

<script>
import VueSelect from 'vue-select';

export default {
  components: { VueSelect },
  props: {
    selectedStatusId: [String, Number, null],
    statuses: Array,
    user: Object,
    date: String,
    timeStart: [Object, String, null],
    timeEnd: [Object, String, null],
    changeMode: Boolean,
  },
  emits: ['update:selectedStatusId', 'openModal', 'sendWorkStatus'],
  data() {
    return {
      innerSelectedId: this.selectedStatusId,
      sickLeaveId: this.statuses.find(status => status.type == 'sick_leave')?.id,
    }
  },
  watch: {
    selectedStatusId(val) {
      this.innerSelectedId = val
    },
  },
  computed: {
    selectedStatus() {
      const status = this.statuses.find(status => status.id === this.selectedStatusId)
      return status;
    },
    sickLeaveDailyStatus() {
      return this.user.daily_work_statuses.find(status => status.work_status_id == this.sickLeaveId);
    },
    needCloseSeakLeave() {
      if (this.selectedStatus.type != 'sick_leave') {
        return false;
      }
      if (this.user.daily_work_statuses.length) {
        return this.sickLeaveDailyStatus && this.sickLeaveDailyStatus.status == 'pending';
      }
    }
  },
  methods: {
    handleChange(val) {
      this.$emit('update:selectedStatusId', val)

      const status = this.statuses.find(s => s.id === val)

      if (!status) {
        this.$emit('sendWorkStatus', { closeEditor: true })
        return
      }

      if (status.type === 'part_time_day') {
        this.$emit('openModal', 'part_time')
        return
      }

      if (status.type === 'sick_leave') {
        this.$emit('openModal', 'sick_leave')
        return
      }

      if (status.type === 'vacation') {
        this.$emit('openModal', 'vacation')
        return
      }

      this.$emit('sendWorkStatus', { closeEditor: true })
    }

  }
}
</script>