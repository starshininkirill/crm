<template>
    <div>
        <div class="text-xl mb-3">Шортлист</div>
        <table v-if="shortlist.length > 0" class="w-full border border-gray-300">
            <thead>
                <tr class="bg-gray-800">
                    <th class="border border-gray-300 px-4 py-2 text-left text-white">
                        Сделка
                    </th>
                    <th class="border border-gray-300 px-4 py-2 text-left text-white">
                        Сумма</th>
                    <th class="border border-gray-300 px-4 py-2 text-left text-white">
                        ИНН</th>
                    <th class="border border-gray-300 px-4 py-2 text-left text-white">
                        Номер платежа</th>
                    <th class="border border-gray-300 px-4 py-2 text-left text-white">
                        Прикрепить
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(waitPayment, idx) in shortlist" :key="idx">
                    <td class="border border-gray-300 px-4 py-2">
                        <Link v-if="waitPayment.contract"
                            :href="route('admin.contract.show', { contract: waitPayment.contract.id })"
                            class="text-blue-700">
                        {{ waitPayment.contract.number }}
                        </Link>
                    </td>
                    <td class="border border-gray-300 px-4 py-2">{{ waitPayment.value }}
                    </td>
                    <td class="border border-gray-300 px-4 py-2">{{ waitPayment.inn }}
                    </td>
                    <td class="border border-gray-300 px-4 py-2">{{ waitPayment.order }}
                    </td>
                    <td @click="attachPayment(waitPayment)"
                        class="border border-gray-300 px-4 py-2 cursor-pointer text-blue-700">
                        Прикрепить</td>
                </tr>
            </tbody>
        </table>
        <div v-else>
            Ожиданий не найдено
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import { router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

export default {
    props: {
        payment: Object,
        load: Boolean,
    },
    data() {
        return { shortlist: [] };
    },
    async mounted() {
        const response = await axios.get(route('payment.shortlist', { payment: this.payment.id }));
        this.shortlist = response.data;
        
        this.$emit("update:load", true);
    },
    methods: {
        attachPayment(oldPayment) {
            router.post(route('payment.shortlist.attach'), {
                oldPayment: oldPayment.id,
                newPayment: this.payment.id,
            })
        },
    }
}
</script>
