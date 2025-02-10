<template>

    <Head title="Ключи Т2 API" />
    <div class="contract-page-wrapper flex flex-col">
        <h1 class="text-4xl font-semibold mb-6">Ключи Т2 API</h1>

        <ul v-if="$page.props.errors && $page.props.errors.length" class="flex flex-col gap-1 mb-4">
            <li v-for="(error, index) in $page.props.errors" :key="index" class="text-red-400">{{ error }}</li>
        </ul>

        <div class="flex gap-4 flex-col">
            <form @submit.prevent="submitForm" class="max-w-md ">
                <FormInput v-model="form.options[0].name" type="hidden" name="access_token_name" />
                <FormInput v-model="form.options[1].name" type="hidden" name="refresh_token_name" />

                <div class=" grid grid-cols-2 gap-4">
                    <FormInput v-model="form.options[0].value" type="text" name="acess_token" placeholder="Acess Token"
                        label="Access Token" />
                    <FormInput v-model="form.options[1].value" type="text" name="refresh_token"
                        placeholder="Refresh Token" label="Refresh Token" />
                </div>
                <button class="btn mt-4" type="submit">
                    Отправить
                </button>
            </form>
            <div class="flex flex-col gap-2">
                <h1 class="text-2xl font-semibold">
                    Импорт статистики по номерам из T2
                </h1>
                <div v-if="lastUpdate">
                    Последнее обновление {{ lastUpdate }}
                </div>
                <div @click="t2LoadData" class="btn !w-fit">
                    Загрузить данные
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { Head, useForm } from '@inertiajs/vue3';
import SaleDepartmentLayout from '../Layouts/SaleDepartmentLayout.vue';
import FormInput from '../../../Components/FormInput.vue';
import { route } from 'ziggy-js';
import { router } from '@inertiajs/vue3';

export default {
    components: {
        Head,
        FormInput
    },
    props: {
        accessToken: {
            type: String,
        },
        refreshToken: {
            type: String,
        },
        lastUpdate: {
            type: String,
        }
    },
    layout: SaleDepartmentLayout,
    setup(props) {
        const form = useForm({
            'options': [
                {
                    'name': 't2_access_token',
                    'value': props.accessToken,
                },
                {
                    'name': 't2_refresh_token',
                    'value': props.refreshToken,
                }
            ]
        })

        const submitForm = () => {
            form.post(route('option.mass-update'), {
                onFinish() {
                    form.options = [
                        {
                            'name': 't2_access_token',
                            'value': props.accessToken,
                        },
                        {
                            'name': 't2_refresh_token',
                            'value': props.refreshToken,
                        }
                    ]
                }
            });
        };

        return {
            form,
            submitForm
        }
    },
    methods: {
        t2LoadData() {
            router.get(route('admin.sale-department.t2-load-data'), {
                dateStart: 'test',
                dateEnd: 'test',
            });

        }
    }
}


</script>