<template>
    <Head :title="`Договор № ${contract.id}`" />
    <div class="contract-page-wrapper flex flex-col">
        <h1 class="text-4xl font-semibold mb-6">Договор: №{{ contract.number }}</h1>
        <div class="flex gap-4">
            <div class="w-2/3 info">
                <div class="text-2xl mb-5">Цена: {{ contract.price }}</div>
                <div class="text-2xl mb-5">
                    ИНН: {{ contract.client?.inn ?? 'Нет данных' }}
                </div>
                <div class="text-2xl mb-2">
                    Услуги:
                </div>
                <div class="flex flex-col gap-2 mb-5">
                    <template v-if="contract.services.length === 0">
                        Услуги пустые
                    </template>
                    <template v-else>
                        <div class="flex flex-col mb-4">
                            <span v-for="(service, index) in contract.services" :key="service.id">
                                {{ service.name }} - {{ service.price }}
                            </span>
                        </div>
                    </template>
                </div>

                <div v-if="contract.payments && contract.payments.length > 0">
                    <h3 class="text-2xl mb-2">Платежи</h3>
                    <div class="flex flex-col gap-1">
                        <Link v-for="payment in contract.payments" :key="payment.id"
                            :href="route('admin.payment.show', { payment: payment.id })" class="w-fit">
                        {{ payment.order }}й платеж: {{ payment.value }}
                        </Link>
                    </div>
                </div>
            </div>
            <div class=" w-1/3">
                Тут можно будет добавлять исполнителя
            </div>
        </div>
    </div>
</template>

<script>
import { Head } from '@inertiajs/vue3';
import ContractLayout from '../Layouts/ContractLayout.vue';

export default {
    components: {
        Head
    },
    props: {
        contract: {
            type: Object
        },
    },
    layout: ContractLayout,
}

</script>