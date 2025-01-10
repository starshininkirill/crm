<template>
    <Link :href="href"
        :class="isActive() ? 'bg-gray-900 text-white' : 'text-gray-300 hover:bg-gray-700 hover:text-white'"
        class="rounded-md px-3 py-2 text-sm font-medium">
    <slot />
    </Link>
</template>

<script>
import { usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

export default {
    name: 'HeaderNavLink',
    props: {
        href: {
            type: String,
            required: true
        },
        route: {
            type: String,
            required: true
        }
    },
    methods: {
        isActive() {           
            const page = usePage();
            const route = this.route

            const currentRoute = page.url.replace(/^\//, ''); 
            const targetRoute = route.replace(/\*$/, '');
            return currentRoute.startsWith(targetRoute);
        }
    },
}
</script>
