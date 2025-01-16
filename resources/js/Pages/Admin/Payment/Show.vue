<template>

    <Head :title="`Платеж №${payment.id}`" />
    <div class="bg-white p-6 rounded-lg shadow-md w-1/3">
        <h1 class="text-3xl font-semibold mb-4">Платеж №: {{ payment.id }}</h1>

        <div class="payment-info text-lg">
            <div class="contract font-semibold mb-3">
                Сделка(Направление):
                <Link v-if="payment.contract" class="text-blue-500 hover:underline"
                    :href="route('admin.contract.show', { contract: payment.contract.id })">
                {{ payment.contract.number }}</Link>
                <template v-else>
                    Не прикреплён
                </template>
            </div>
            <div class="value mb-3">
                <span class="font-semibold">Сумма:</span> {{ payment.value }}
            </div>
            <div class="status mb-3">
                <span class="font-semibold">Статус: </span>
                <span :class="payment.status == paymentStatuses.close ? 'text-green-600' : 'text-red-600'">
                    {{ payment.formatStatus }}
                </span>
            </div>
            <div class="status mb-3">
                <span class="font-semibold">Тип (Новые/Старые): </span>
                <span>
                    {{ payment.type }}
                </span>
            </div>
            <div class="date mb-3">
                <span class="font-semibold">Метод оплаты:</span>
                {{ payment.method }}
            </div>
            <div class="date mb-3">
                <span class="font-semibold">Технический платёж:</span> {{ payment.is_technical ? 'Да' : 'Нет' }}
            </div>
            <div class="date mb-3">
                <span class="font-semibold">Когда подтверждён:</span>
                {{ payment.confirmed_at }}
            </div>
            <div class="date mb-3">
                <span class="font-semibold">Создан:</span> {{ payment.created_at }}
            </div>
            <div class="date">
                <span class="font-semibold">Ответственный: </span>

                <a v-if="payment.responsible" class="text-blue-500 hover:underline"
                    href="{{ route('admin.user.show', $payment->responsible->id) }}">
                    {{ payment.responsible.first_name }} {{ payment.responsible.last_name }}
                </a>
                <template v-else>
                    Не прикреплён
                </template>
            </div>
        </div>
    </div>
</template>

<script>
import { Head } from '@inertiajs/vue3';
import PaymentLayout from '../Layouts/PaymentLayout.vue';

export default {
    components: {
        Head
    },
    props: {
        payment: {
            type: Object
        },
        paymentStatuses: {
            type: Object
        },
    },
    layout: PaymentLayout,
}

</script>