<template>

    <Head title="Авторизация" />

    <form @submit.prevent="submitForm" method="post"
        class="p-6 border rounded-md max-w-md w-full m-auto mt-36">
        <h2 class=" mb-5 text-4xl font-bold">
            Вход
        </h2>

        <Error />

        <div class="flex flex-col gap-2">
            <FormInput v-model="form.email" type="email" name="email" placeholder="Почта" label="Почта"
                autocomplete="email" required />
            <FormInput v-model="form.password" type="password" name="password" placeholder="******" label="Пароль"
                autocomplete="current-password" required />
            <button type="submit" class="btn" data-ripple-light="true">
                Войти
            </button>
        </div>

        <div class=" mt-4">
            Войти как <span class=" italic font-semibold cursor-pointer" @click="loginSale">sale1@mail.ru</span>
        </div>
    </form>
</template>

<script>
import { Head, useForm } from '@inertiajs/vue3';
import FormInput from '../../Components/FormInput.vue';
import { route } from 'ziggy-js';
import Error from '../../Components/Error.vue'

export default {
    name: 'LoginForm',
    components: {
        Head,
        FormInput,
        Error,
    },
    methods: {
        loginSale(){
            this.form.email = 'sale1@mail.ru';
            this.form.password = '1409199696Rust';
        }
    },
    setup() {
        const form = useForm({
            'email': null,
            'password': null,
        });

        const submitForm = () => {
            form.post(route('login.attempt'), {
                onFinish: () => {
                    form.password = null;
                },

            });
        };

        return {
            form,
            submitForm
        }
    },
};

</script>