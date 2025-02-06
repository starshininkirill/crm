<template>
    <tr v-show="load">
        <td colspan="8" class="border border-gray-300 px-4 py-4">
            <div class="text-xl font-semibold mb-3">Прикрепить платёж</div>
            <div class="grid gap-5" :class="withShortList ? 'grid-cols-2' : 'grid-cols-1'">
                <ContractSearch :payment="payment" @close="handleClose" />
                <Shortlist v-if="withShortList" v-model:load="load" @close="handleClose" :payment="payment" />
            </div>
        </td>
    </tr>
</template>

<script>
import ContractSearch from './ContractSearch.vue';
import Shortlist from './Shortlist.vue';

export default {
    components: { ContractSearch, Shortlist },
    props: {
        payment: Object,
        withShortList: {
            type: Boolean,
            default: true,
        },
    },
    data() {
        let load = this.withShortList ? false : true;
        return {
            load: load,
        }
    },
    methods: {
        handleClose() {
            this.load = false;
            this.$emit('close');
        }
    }
}
</script>