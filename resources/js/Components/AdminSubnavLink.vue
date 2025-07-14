<template>
    <Link :href="href" :class="[{ 'text-white bg-gray-800': isActive() }, 'px-4 py-2 border border-b-0']">
    <slot />
    </Link>
</template>

<script>
import { Link, usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

export default {
    name: 'HeaderNavLink',
    props: {
        href: {
            type: String,
            required: true,
        },
        route: {
            type: String,
            required: true,
        },
    },
    methods: {
        isActive() {
            const baseRoute = this.route.replace(/\.index$/, '');
            const currentRoute = route().current();

            return currentRoute === this.route || currentRoute.startsWith(baseRoute + '.');
        },
    },
};
</script>