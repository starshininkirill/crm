<template>
    <tr v-show="load">
        <td colspan="8" class="border border-gray-300 px-4 py-4">
            <div class="text-xl font-semibold mb-3">Прикрепить платёж</div>
            <div class="grid grid-cols-2 gap-5">
                <ContractSearch :payment="payment" @close="handleClose"/>
                <Shortlist v-model:load="load" :payment="payment" />
            </div>
        </td>
    </tr>
</template>

<script>
import ContractSearch from './ContractSearch.vue';
import Shortlist from './Shortlist.vue';

export default {
    components: { ContractSearch, Shortlist },
    props: { payment: Object },
    data(){
        return {
            load: false,
        }
    },
    methods: {
        handleClose() {
            console.log('close attach');
            
            this.load = false;
            this.$emit('close');
        }
    }
}
</script> 