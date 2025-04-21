<template>
    <PaymentLayout>

        <Head :title="`Платеж №${payment.id}`" />
        <div class="bg-white p-6">
            <div class=" flex max-w-lg items-center justify-between mb-4">
                <h1 class="text-3xl font-semibold">Платеж №: {{ payment.id }}</h1>
                <div class="btn !w-fit" @click="showMode = !showMode">
                    {{ showMode ? 'Редактировать' : 'Назад' }}
                </div>
            </div>
            <ShowForm v-if="showMode" :payment="payment" :paymentStatuses="paymentStatuses" />
            <EditForm v-if="!showMode" :payment="payment" :paymentStatuses="paymentStatuses" :paymentTypes="paymentTypes"
                :organizations="organizations" :users="users"/>
        </div>
    </PaymentLayout>
</template>


<script>
import { Head } from '@inertiajs/vue3';
import PaymentLayout from '../Layouts/PaymentLayout.vue';
import ShowForm from './Components/ShowForm.vue';
import EditForm from './Components/EditForm.vue';

export default {
    components: {
        Head,
        PaymentLayout,
        ShowForm,
        EditForm
    },
    props: {
        payment: {
            type: Object
        },
        paymentStatuses: {
            type: Object
        },
        paymentTypes: {
            required: true,
            type: Object
        },
        organizations: {
            required: true,
            type: Array,
        },
        users: {
            required: true,
            type: Array,
        },
    },
    data() {
        return {
            showMode: true,
        }
    },
}

</script>