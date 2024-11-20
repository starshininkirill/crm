<template>
    <div class="flex flex-col w-full mb-6">
        <div class="flex flex-col rounded-md border border-gray-400 shadow-xl">
            <div class="bg-gray-800 p-2 rounded-md text-white font-semibold text-xl">
                Суммы
            </div>
            <div v-show="showForm" class="flex flex-col gap-4 p-2 mt-2">
                <div class="grid grid-cols-2 gap-2">
                    <vue-form-input min="0" @change="handleSaleChange" v-model="localSale" type="number" name="sale"
                        placeholder="Скидка" label="Скидка" />
                    <vue-form-input readonly min="0" v-model="computedAmountPrice" type="number" name="amount_price"
                        placeholder="Общая сумма" label="Общая сумма" />
                    <vue-form-input readonly v-model="amountDuration" type="number" name="development_time"
                        placeholder="В разработке" label="Срок оказания услуг(раб. дней)" />
                </div>
                <div class="text-2xl font-semibold">
                    Платежи
                </div>
                <div class="grid grid-cols-2 gap-2">
                    <vue-form-input v-for="(payment, index) in payments" :key="index" type="number"
                        :name="'payments[' + index + ']'" v-model="payments[index]"
                        :placeholder="'Платёж ' + (index + 1)" :label="'Платёж ' + (index + 1)" />
                </div>
                <div class="text-xl font-semibold">
                    Сплит платежей
                </div>
                <div class="flex gap-3">
                    <div class="btn" @click="splitPayments(40, 30, 30)">
                        40/30/30
                    </div>
                    <div class="btn" @click="splitPayments(30, 40, 30)">
                        30/40/30
                    </div>
                    <div class="btn" @click="splitPayments(50, 50)">
                        50/50
                    </div>
                    <div class="btn" @click="splitPayments(100)">
                        100
                    </div>
                </div>
                <button type="submit" class="btn">Отправить</button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        servicePrices: {
            type: Array,
            required: true
        },
        modelValue: {
            type: Number,
            required: true
        },
        amountPrice: {
            type: Number,
            required: true
        },
        old: {
            type: Object,
            default: [],
        },
    },
    data() {
        const paymentsFromOld = Array.isArray(this.old.payments) ? this.old.payments : [];

        return {
            showForm: true,
            localSale: this.modelValue,
            localAmountPrice: this.amountPrice,
            payments: Array.from({ length: 5 }, (_, index) => paymentsFromOld[index] || 0),
        };
    },
    computed: {
        computedAmountPrice: {
            get() {
                return this.localAmountPrice;
            },
            set(value) {
                this.localAmountPrice = value;
                this.$emit('update:amountPrice', value);
            }
        },
        amountDuration() {
            return this.servicePrices.reduce((accumulator, currentValue) => accumulator + parseInt(currentValue.duration), 0);
        }
    },
    watch: {
        modelValue(newValue) {
            this.localSale = newValue;
        },
        amountPrice(newValue) {
            this.localAmountPrice = newValue;
        },
    },
    methods: {
        handleSaleChange(event) {
            const value = Number(event.target.value);
            this.localSale = value;
            this.$emit('update:modelValue', value);
        },
        splitPayments(...args) {
            if (args.length === 0) {
                return;
            }

            if (this.localAmountPrice === 0) {
                this.payments = this.payments.map(() => 0);
                return;
            }

            this.payments.fill(0);

            const th = this

            args.forEach((arg, idx) => {
                const value = this.localAmountPrice / 100 * arg;
                if (idx < this.payments.length) {
                    th.payments[idx] = value;
                }
            });

        }
    }
};
</script>