<template>
    <div class="flex flex-col w-full mb-6">
        <div class="flex flex-col rounded-md border border-gray-400 shadow-xl">
            <div class="bg-gray-800 p-2 rounded-md text-white font-semibold text-xl">
                Суммы
            </div>
            <div class="flex flex-col gap-4 p-2 mt-2">
                <div class="grid grid-cols-2 gap-2">
                    <FormInput min="0" v-model="form.sale" type="number" name="sale" placeholder="Скидка"
                        label="Скидка" />
                    <FormInput readonly min="0" v-model="form.amount_price" type="number" name="amount_price"
                        placeholder="Общая сумма" label="Общая сумма" />
                    <FormInput readonly v-model="form.development_time" type="number" name="development_time"
                        placeholder="В разработке" label="Срок оказания услуг(раб. дней)" />
                </div>
                <div class="text-2xl font-semibold">
                    Платежи
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <FormInput v-for="(payment, index) in form.payments" :key="index" type="number"
                        :name="'payments[' + index + ']'" v-model="form.payments[index]"
                        :placeholder="'Платёж ' + (index + 1)" :label="'Платёж ' + (index + 1)" />
                </div>
                <div class="text-xl font-semibold">
                    Сплит платежей
                </div>
                <div class="flex gap-3">
                    <div class="btn !w-fit" @click="splitPayments(40, 30, 30)">
                        40/30/30
                    </div>
                    <div class="btn !w-fit" @click="splitPayments(30, 40, 30)">
                        30/40/30
                    </div>
                    <div class="btn !w-fit" @click="splitPayments(50, 50)">
                        50/50
                    </div>
                    <div class="btn !w-fit" @click="splitPayments(100)">
                        100
                    </div>
                </div>
                <button type="submit" @click="handleSubmit" :disabled="isSubmitting" class="btn !w-fit">
                    {{ isSubmitting ? 'Генерация документа...' : 'Отправить' }}
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import FormInput from '../../../Components/FormInput.vue';
import { route } from 'ziggy-js';

export default {
    components: {
        FormInput,
    },
    props: {
        form: {
            type: Object,
            required: true,
        },
    },
    data() {
        this.form.payments = Array.from({ length: 3 }, (_, index) => this.form.payments[index] || 0)
        return {
            isSubmitting: false,
        };
    },
    methods: {
        splitPayments(...args) {
            if (args.length == 0) {
                return;
            }

            if (this.form.amount_price == 0) {
                this.form.payments = this.form.payments.map(() => 0);
                return;
            }

            this.form.payments.fill(0);

            const th = this

            args.forEach((arg, idx) => {
                const value = this.form.amount_price / 100 * arg;
                if (idx < this.form.payments.length) {
                    th.form.payments[idx] = value;
                }
            });
        },
        handleSubmit(event) {
            event.preventDefault();
            this.isSubmitting = true;
            let th = this;
            this.form.post(route('lk.contract.store'), {
                onFinish(){
                    th.isSubmitting = false;
                },
            });
        },
    }
};
</script> 