<template>

    <Head title="Ключи Т2 API" />
    <div class="contract-page-wrapper flex flex-col">
        <h1 class="text-4xl font-semibold mb-6">Ключи Т2 API</h1>

        <ul v-if="form.errors" class="flex flex-col gap-1 mb-4">
            <li v-for="(error, index) in form.errors" :key="index" class="text-red-400">{{ error }}</li>
        </ul>


        <form @submit.prevent="submitForm" class="max-w-md ">
            <FormInput v-model="form.options[0].name" type="hidden" name="access_token_name" />
            <FormInput v-model="form.options[1].name" type="hidden" name="refresh_token_name" />

            <div class=" grid grid-cols-2 gap-4">
                <FormInput v-model="form.options[0].value" type="text" name="acess_token" placeholder="Acess Token"
                    label="Acess Token" />
                <FormInput v-model="form.options[1].value" type="text" name="refresh_token" placeholder="Refresh Token"
                    label="Refresh Token" />
            </div>
            <button class="btn mt-4" type="submit">
                Отправить
            </button>
        </form>
    </div>
</template>

<script>
import { Head, useForm } from '@inertiajs/vue3';
import SaleDepartmentLayout from '../Layouts/SaleDepartmentLayout.vue';
import FormInput from '../../../Components/FormInput.vue';

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
    }
}


</script>