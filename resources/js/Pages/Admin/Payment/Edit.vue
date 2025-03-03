<template>
    <PaymentLayout>

        <Head :title="`Платеж №${payment.id}`" />
        <div class="bg-white p-6">
            <div class=" flex max-w-md items-center justify-between mb-4">
                <h1 class="text-3xl font-semibold">Платеж №: {{ payment.id }}</h1>
                <Link :href="route('admin.payment.show', payment.id)" class="btn !w-fit">
                Назад
                </Link>
            </div>
            <form @submit.prevent="submitForm"
                class="payment-info text-lg flex flex-col gap-4 p-4 bg-white border border-gray-200 rounded-md max-w-lg w-full">

                <Error />

                <div class="font-semibold grid grid-cols-2 gap-3 max-w-sm">
                    Сделка(Направление):
                    <Link v-if="payment.contract" class="text-blue-500 hover:underline"
                        :href="route('admin.contract.show', { contract: payment.contract.id })">
                    {{ payment.contract.number }}
                    </Link>
                    <template v-else>
                        Не прикреплён
                    </template>
                </div>

                <FormInput v-model="form.value" type="text" name="value" placeholder="Сумма" label="Сумма"
                    autocomplete="value" />

                <FormInput v-model="form.inn" type="text" name="inn" placeholder="ИНН" label="ИНН" autocomplete="inn" />

                <div class="flex flex-col gap-1">
                    <div class="label">
                        Статус
                    </div>
                    <VueSelect v-model="form.status" :reduce="status => status.id" label="name"
                        :options="formattedPaymentStatuses">
                    </VueSelect>
                </div>

                <div class="flex flex-col gap-1">
                    <div class="label">
                        Тип (Новые/Старые)
                    </div>
                    <VueSelect v-model="form.type" :reduce="status => status.id" label="name"
                        :options="formattedPaymentTypes">
                    </VueSelect>
                </div>

                <div class="flex flex-col gap-1">
                    <div class="label">
                        Технический платёж
                    </div>
                    <VueSelect v-model="form.is_technical" :reduce="texnical => texnical.id" label="name"
                        :options="texnical">
                    </VueSelect>
                </div>


                <div class="flex flex-col gap-1">
                    <div class="label">
                        Ответственный
                    </div>
                    <VueSelect v-model="form.responsible_id" :reduce="user => user.id" label="full_name"
                        :options="users">
                    </VueSelect>
                </div>

                <div class="flex flex-col gap-1">
                    <div class="label">
                        Организация
                    </div>
                    <VueSelect v-model="form.organization_id" :reduce="organization => organization.id"
                        label="short_name" :options="organizations">
                    </VueSelect>
                </div>

                <FormInput v-model="form.confirmed_at" type="datetime-local" name="confirmed_at"
                    label="Когда потдверждён" />

                <FormInput v-model="form.created_at" type="datetime-local" name="created_at" label="Создан" />

                <button class="btn">
                    Сохранить
                </button>
            </form>
        </div>
    </PaymentLayout>
</template>


<script>
import { Head, useForm } from '@inertiajs/vue3';
import PaymentLayout from '../Layouts/PaymentLayout.vue';
import FormInput from '../../../Components/FormInput.vue';
import VueSelect from 'vue-select';
import Error from '../../../Components/Error.vue';

export default {
    components: {
        Head,
        FormInput,
        VueSelect,
        Error,
        PaymentLayout
    },
    props: {
        payment: {
            required: true,
            type: Object
        },
        paymentStatuses: {
            required: true,
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
        let form = useForm({
            'value': this.payment.value,
            'inn': this.payment.inn || '',
            'status': this.payment.status,
            'type': this.payment.type,
            'is_technical': this.payment.is_technical,
            'confirmed_at': this.payment.confirmed_at,
            'created_at': this.payment.created_at,
            'organization_id': this.payment.organization_id,
            'responsible_id': this.payment.responsible_id,
        })

        let texnical = [
            {
                'id': 1,
                'name': 'Да'
            },
            {
                'id': 0,
                'name': 'Нет'
            }
        ]
        return {
            form,
            texnical
        }
    },
    methods: {
        submitForm() {
            this.form.patch(route('admin.payment.update', this.payment));
        }
    },
    computed: {
        formattedPaymentStatuses() {
            return Object.keys(this.paymentStatuses).map(key => ({
                id: parseInt(key),
                name: this.paymentStatuses[key],
            }));
        },
        formattedPaymentTypes() {
            return Object.keys(this.paymentTypes).map(key => ({
                id: parseInt(key),
                name: this.paymentTypes[key],
            }));
        },
    },
}

</script>