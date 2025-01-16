<template>
    <Link :href="href" :class="[{ 'text-white bg-gray-800': isActive() }, 'px-4 py-2 border border-b-0']">
    <slot />
    </Link>
</template>

<script>
import { Link, usePage } from '@inertiajs/vue3';
import { route as ziggyRoute } from 'ziggy-js';

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
            const page = usePage();
            const currentRoute = page.props.ziggy.location;
            const targetRoute = ziggyRoute(this.route);

            return currentRoute === targetRoute;
        },
    },
};
</script>