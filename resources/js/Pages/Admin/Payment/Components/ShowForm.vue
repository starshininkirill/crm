<template>
    <div class="payment-info text-lg flex flex-col gap-4 p-4 bg-white border border-gray-200 rounded-md max-w-lg">
        <div class="contract font-semibold grid grid-cols-2 gap-3 max-w-sm">
            Сделка(Направление):
            <Link v-if="payment.contract" class="text-blue-500 hover:underline"
                :href="route('admin.contract.show', { contract: payment.contract.id })">
            {{ payment.contract.number }}
            </Link>
            <template v-else>
                Не прикреплён
            </template>
        </div>
        <div class="value grid grid-cols-2 gap-3 max-w-sm">
            <span class="font-semibold">Сумма:</span>
            <span>
                {{ formatPrice(payment.value) }}
            </span>
        </div>
        <div class="status grid grid-cols-2 gap-3 max-w-sm">
            <span class="font-semibold">ИНН: </span>
            <span>
                {{ payment.inn ?? 'Не определён' }}
            </span>
        </div>
        <div class="status grid grid-cols-2 gap-3 max-w-sm">
            <span class="font-semibold">Статус: </span>
            <span :class="payment.status == paymentStatuses.close ? 'text-green-600' : 'text-red-600'">
                {{ payment.formatStatus }}
            </span>
        </div>
        <div class="status grid grid-cols-2 gap-3 max-w-sm">
            <span class="font-semibold">Тип (Новые/Старые): </span>
            <span>
                {{ payment.type }}
            </span>
        </div>
        <div class="date grid grid-cols-2 gap-3 max-w-sm">
            <span class="font-semibold">Технический платёж:</span>
            <span>
                {{ payment.is_technical ? 'Да' : 'Нет' }}
            </span>
        </div>
        <div class="date grid grid-cols-2 gap-3 max-w-sm">
            <span class="font-semibold">Ответственный: </span>
            <Link v-if="payment.responsible" class="text-blue-500 hover:underline"
                :href="route('admin.user.show', payment.responsible.id)">
            {{ payment.responsible.first_name }} {{ payment.responsible.last_name }}
            </Link>
            <template v-else>
                Не прикреплён
            </template>
        </div>
        <div class="date grid grid-cols-2 gap-3 max-w-sm">
            <span class="font-semibold">Организация: </span>
            <Link v-if="payment.organization" class="text-blue-500 hover:underline"
                :href="route('admin.organization.show', payment.organization.id)">
            {{ payment.organization.short_name }}
            </Link>
            <template v-else>
                Не прикреплёна
            </template>
        </div>
        <div class="date grid grid-cols-2 gap-3 max-w-sm">
            <span class="font-semibold">Когда подтверждён:</span>
            <span>
                {{ payment.confirmed_at }}
            </span>
        </div>
        <div class="date grid grid-cols-2 gap-3 max-w-sm">
            <span class="font-semibold">Создан:</span>
            <span>
                {{ payment.created_at }}
            </span>
        </div>
    </div>
</template>

<script>
import { Head } from '@inertiajs/vue3';
import PaymentLayout from '../../Layouts/PaymentLayout.vue';

export default {
    components: {
        Head,
        PaymentLayout
    },
    props: {
        payment: {
            type: Object
        },
        paymentStatuses: {
            type: Object
        },
    },
}

</script>