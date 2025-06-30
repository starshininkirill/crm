<template>
    <h1 class="text-2xl font-semibold mb-6">
        Компенсации и лишения
    </h1>
    <div class="flex flex-col gap-4">
        <table class="table" v-if="bonuses.length || (user.lates_penalty != 0 && half == 'first_half')">
            <thead class="thead">
                <tr>
                    <th scope="col" class="px-2 py-2 border-x ">
                        Сумма
                    </th>
                    <th scope="col" class="px-2 py-2 border-x">
                        Описание
                    </th>
                    <th scope="col" class="px-2 py-2 border-x">
                        Тип
                    </th>
                    <th scope="col" class="px-2 py-2 border-x">
                        Удалить
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr class="table-row" v-if="user.lates_penalty != 0 && half == 'first_half'">
                    <td class="px-2 border-r py-2 text-gray-900">
                        - {{ formatPrice(user.lates_penalty) }}
                    </td>
                    <td class="px-2 border-r py-2 text-gray-900">
                        Вычет за опоздания
                    </td>
                    <td class="px-2 border-r py-2 text-gray-900">
                        Лишение
                    </td>
                    <td class="px-2 border-r py-2 text-gray-900">
                    </td>
                </tr>
                <tr class="table-row" v-for="bonus in bonuses">
                    <td class="px-2 border-r py-2 text-gray-900">
                        {{ bonus.type == 'penalty' ? '-' : '' }} {{ formatPrice(bonus.value) }}
                    </td>
                    <td class="px-2 border-r py-2 text-gray-900">
                        {{ bonus.description }}
                    </td>
                    <td class="px-2 border-r py-2 text-gray-900">
                        {{ bonus.type == 'bonus' ? 'Бонус' : 'Лишение' }}
                    </td>
                    <td class="px-2 border-r py-2 text-gray-900">
                        <button @click="deleteBonus(bonus)" class="text-red-600 hover:underline">
                            Удалить
                        </button>
                    </td>
                </tr>
                <tr class="table-row">
                    <td colspan="4" class="px-2 border-r py-2 text-gray-900">
                        Итого: {{ formatPrice(adjustments) }}
                    </td>
                </tr>
            </tbody>
        </table>

        <Error />

        <div class="grid grid-cols-3 gap-2">

            <FormInput v-model="form.value" type="number" name="value" placeholder="Сумма" required />

            <FormInput v-model="form.description" type="text" name="description" placeholder="Описание" required />

            <VueSelect class=" mt-auto" v-model="form.type" :reduce="type => type.value" label="name" :options="types">
            </VueSelect>

            <div class="btn col-span-3" @click="submitForm()">
                Создать
            </div>
        </div>
    </div>

</template>
<script>
import { useForm, router } from '@inertiajs/vue3';
import { route } from 'ziggy-js';
import VueSelect from 'vue-select';
import FormInput from '../../../../Components/FormInput.vue';
import Error from '../../../../Components/Error.vue';

export default {
    components: {
        VueSelect,
        FormInput,
        Error
    },
    props: {
        user: {
            type: Object,
            required: true,
        },
        half: {
            type: String,
            required: true,
        },
        date: {
            type: String
        },
    },
    data() {

        let types = [
            {
                'name': 'Компенсация',
                'value': 'bonus'
            },
            {
                'name': 'Лишение',
                'value': 'penalty'
            },
        ]

        let form = useForm({
            'user_id': this.user.id,
            'period': this.half,
            'date': this.date,
            'type': types[0].value,
            'value': null,
            'description': null,
        });

        return {
            form,
            types,
            bonuses: this.user[this.half],
            adjustments: this.user[this.half + '_adjustments'],
        }
    },
    emits: ['closeModal'],
    methods: {
        submitForm() {
            const context = this;
            this.form.post(route('admin.time-sheet.user-adjustment.store'), {
                preserveState: true,
                preserveScroll: true,
                onSuccess: () => {
                    context.$emit('closeModal');

                    // this.$inertia.reload({
                    //     only: ['adjustments'],
                    // });
                },
            });
        },
        deleteBonus(adjustment) {
            const context = this;
            if (confirm('Вы уверены, что хотите удалить выплату?')) {
                router.delete(route('admin.time-sheet.user-adjustment.destroy', adjustment), {
                    onSuccess: () => {
                        context.$emit('closeModal');
                    },
                });
            }
        }
    }
}
</script>