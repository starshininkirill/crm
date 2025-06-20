<template>
    <header class="bg-gray-800 border-b border-white">
        <div class="mx-auto container px-2">
            <div class="flex h-16 items-center justify-between">
                <div class="flex items-center justify-between w-full">
                    <div class=" flex items-baseline space-x-4">
                        <HeaderNavLink :strictMode="true" :href="route('home')" route="home">Главная</HeaderNavLink>
                        <HeaderNavLink v-if="$page.props.user" :href="route('lk')" route="lk">
                            Личный кабинет
                        </HeaderNavLink>
                        <HeaderNavLink v-if="$page.props.user" :href="route('admin')" route="admin">Админка
                        </HeaderNavLink>
                    </div>

                    <TimeCheck v-if="$page.props.user" />

                    <div class="flex items-center space-x-4">
                        <HeaderNavLink v-if="!$page.props.user" :href="route('login')" route="login">Вход
                        </HeaderNavLink>
                        <HeaderNavLink v-if="!$page.props.user" :href="route('fastLogin')" route="fastLogin">
                            Войти как админ
                        </HeaderNavLink>
                        <span v-if="$page.props.user" class=" text-l text-white ">
                            {{ $page.props.user.first_name }}
                        </span>
                        <HeaderNavLink v-if="$page.props.user" :href="route('logout')" route="logout">Выйти
                        </HeaderNavLink>
                    </div>
                </div>
            </div>
        </div>
    </header>
</template>

<script>
import { route } from 'ziggy-js';
import { router } from '@inertiajs/vue3';
import HeaderNavLink from '../Components/HeaderNavLink.vue';
import TimeCheck from '../Components/TimeCheck.vue'

export default {
    name: "Header",
    components: {
        HeaderNavLink,
        TimeCheck
    },
    methods: {
        logout() {
            router.post(`/logout`, {
                onSuccess: () => {
                    console.log('test');
                },
                onError: () => {
                    alert('При попытке выхода возникла ошибка');
                }
            });
        }
    }
};
</script>
