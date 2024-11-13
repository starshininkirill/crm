<template>
    <div class="flex flex-col w-full mb-6">
        <div class="flex flex-col rounded-md border border-gray-400 shadow-xl">
            <div @click="toggleForm" class="bg-gray-800 p-2 rounded-md text-white font-semibold text-xl cursor-pointer">
                Суммы
            </div>
            <div v-show="showForm" class="flex flex-col gap-4 p-2 mt-2">
                <div class="grid grid-cols-2 gap-2">
                    <vue-form-input v-model="amountPrice" type="number" name="amount_price" placeholder="Общая сумма" label="Общая сумма" />
                    <vue-form-input type="text" name="test" placeholder="В разработке" label="Срок оказания услуг(В разработке)" />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: {
        servicePrices:{
            type: Array,
            required: true
        },
    },
    data() {
        return {
            showForm: true,
        };
    },
    computed: {
        amountPrice() {
            return this.servicePrices.reduce((accumulator, currentValue) => parseInt(accumulator) + parseInt(currentValue), 0);
        }
    },
    methods: {
        toggleForm() {
            this.showForm = !this.showForm;
        }
    }
};
</script>