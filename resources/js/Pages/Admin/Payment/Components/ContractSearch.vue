<template>
    <div>
        <FormInput v-model="search" :disabled="isSearching" @input="onSearchInput" name="search" type="number"
            placeholder="Номер договора" label="Поиск по договору" />

        <div v-if="contract && contract.length != 0">
            <div class="text-xl font-semibold py-2 mb-2">
                Информация успешно найдена!
            </div>
            <div class="flex flex-col gap-2">
                <div class=" border-b pb-1 grid grid-cols-2">
                    Номер договора:
                    <Link :href="route('admin.contract.show', { contract: contract.id })"
                        class="font-semibold text-blue-500">
                    {{ contract.number }}
                    </Link>
                </div>
                <div class=" border-b pb-1 grid grid-cols-2">
                    Дата договора:
                    <span class="font-semibold">
                        {{ contract.created_at }}
                    </span>
                </div>
                <div class=" border-b pb-1 grid grid-cols-2">
                    Имя клиента/организации:
                    <span class="font-semibold">
                        {{
                            contract.client_name }}
                    </span>
                </div>
                <div class=" border-b pb-1 grid grid-cols-2">
                    ИНН:
                    <span class="font-semibold">
                        {{ contract.inn }}
                    </span>
                </div>
                <div class=" border-b pb-1 grid grid-cols-2">
                    Сумма договора:
                    <span class="font-semibold">
                        {{ contract.amount_price }}
                    </span>
                </div>
                <div class=" border-b pb-1 grid grid-cols-2">
                    Услуги:
                    <div class="flex flex-wrap">
                        <div class="font-semibold w-fit" v-if="contract.services.length"
                            v-for="(service, idx) in contract.services" :key="service.id">
                            {{ service.name }}<span v-if="idx != contract.services.length - 1">
                                ,
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-4">
                <div v-if="contract.payments">
                    <div class="text-2xl font-semibold mb-2">
                        Платежи
                    </div>
                    <div v-for="searchPayment in contract.payments"
                        class=" border-b py-2 grid grid-cols-2 font-semibold">
                        {{ searchPayment.value }} ( № {{ searchPayment.order }} )
                        <div>
                            <div v-if="searchPayment.close" class=" text-green-500">
                                Оплачен
                            </div>
                            <span v-else @click="attachPayment(searchPayment)" class="cursor-pointer text-blue-700">
                                Прикрепить</span>
                        </div>
                    </div>
                </div>
                <div v-else class="text-xl font-semibold py-2 mb-2">
                    Платежей не найдено
                </div>
            </div>
            <div v-if="contract.childs.length" class="mt-4">
                <div class="text-xl font-semibold py-2 mb-2">
                    Дочерние допродажи
                </div>
                <div class="flex flex-col">
                    <div v-for="subContract in contract.childs" :key="subContract.id"
                        class="py-2 border-b flex flex-col gap-2">
                        <div class="flex justify-between items-center">
                            <Link :href="route('admin.contract.show', { contract: subContract.id })"
                                class=" text-blue-500 text-2xl">
                            Допродажа: {{ subContract.number }}
                            </Link>
                            <div>
                                Дата: {{ subContract.created_at }}
                            </div>
                        </div>
                        <div>
                            Сумма {{ subContract.amount_price }}
                        </div>
                        <div v-if="subContract.payments.length">
                            <div class="text-xl font-semibold mb-2">
                                Платежи
                            </div>
                            <div v-for="subContractPayment in subContract.payments"
                                class="flex justify-between items-center bg-gray-50 p-3 border-b-white">
                                <Link :href="route('admin.payment.show', { payment: subContractPayment.id })"
                                    class=" text-blue-500">
                                {{ subContractPayment.value }} ( № {{ subContractPayment.order }} )
                                </Link>
                                <div>
                                    <div v-if="subContractPayment.close" class=" text-green-500">
                                        Оплачен
                                    </div>
                                    <span v-else @click="attachPayment(subContractPayment)"
                                        class="cursor-pointer text-blue-700">
                                        Прикрепить</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div v-else class="font-semibold py-2 mb-2">
            Договор не найден
        </div>
    </div>
</template>

<script>
import FormInput from '../../../../Components/FormInput.vue';
import axios from 'axios';
import { route } from 'ziggy-js';
import { router } from '@inertiajs/vue3';

export default {
    components: { FormInput },
    props: { payment: Object },
    data() {
        return {
            search: '',
            contract: null,
            isSearching: false,
            searchTimer: null
        };
    },
    methods: {
        attachPayment(oldPayment) {
            if (confirm('Вы уверены, что хотите привязать этот платёж?')) {
                router.post(route('admin.payment.shortlist.attach'), {
                    oldPayment: oldPayment.id,
                    newPayment: this.payment.id,
                }, {
                    onSuccess: () => {
                        console.log('Платеж успешно прикреплен');
                        this.$emit('close');
                    },
                    onError: (error) => {
                        console.error('Ошибка прикрепления платежа:', error);
                    }
                });
            }
        },
        onSearchInput() {
            if (this.searchTimer) {
                clearTimeout(this.searchTimer);
            }

            this.isSearching = false;

            this.searchTimer = setTimeout(() => {
                this.searchContract();
            }, 1000);
        },
        async searchContract() {
            this.isSearching = true;

            if (!this.search) {
                this.contract = null;
                this.isSearching = false;
                return;
            }

            try {
                const response = await axios.get(route('admin.payment.search-contract'), {
                    params: { s: this.search }
                });

                if (response.data.error) {
                    this.contract = [];
                } else {
                    this.contract = response.data;
                }
            } catch (error) {
                console.error('Ошибка при поиске контракта:', error);
                this.contract = null;
            } finally {
                this.isSearching = false;
            }
        }
    }
};
</script>
