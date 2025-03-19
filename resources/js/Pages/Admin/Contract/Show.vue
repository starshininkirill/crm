<template>
    <ContractLayout>
 
        <Head :title="`Договор № ${contract.id}`" />

        <div class="contract-page-wrapper flex flex-col">
            <h1 class="text-4xl font-semibold mb-6">
                <template v-if="contract.parent">
                    Дочерний платеж {{ contract.number }} <br>
                </template>
                <template v-else>
                    Договор: №{{ contract.number }}
                </template>
            </h1>

            <div class="grid grid-cols-3 pr-4">

                <ContractInfo :contract="contract" />

                <Performers :users="users" :contract="contract" />

            </div>
        </div>
    </ContractLayout>
</template>

<script>
import { Head, useForm } from '@inertiajs/vue3';
import ContractLayout from '../Layouts/ContractLayout.vue';
import VueSelect from 'vue-select';
import Performers from './Components/Performers.vue';
import ContractInfo from './Components/ContractInfo.vue';

export default {
    components: {
        Head,
        VueSelect,
        Performers,
        ContractInfo,
        ContractLayout
    },
    props: {
        contract: {
            type: Object,
            required: true,
        },
        users: {
            type: Array,
            required: true,
        },
    },
}

</script>