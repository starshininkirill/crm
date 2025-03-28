<template>
    <BaseLayout>
        <div class="grow container mx-auto w-full py-4 px-2" id="lk-page">
            <div class="text-sm font-medium text-center text-gray-500 border-b border-gray-200   mb-8">
                <ul class="flex flex-wrap -mb-px">
                    <li class="me-2">
                        <Link :href="route('lk.contract.create')" :class="[
                            'inline-block p-4 rounded-t-lg border-b-2',
                            isActive('lk.contract.create')
                                ? 'text-blue-600 border-blue-600  '
                                : 'border-transparent hover:text-gray-600 hover:border-gray-300 '
                        ]">
                        Договор
                        </Link>
                    </li>

                    <li class="me-2">
                        <Link :href="route('lk.act.create')" :class="[
                            'inline-block p-4 rounded-t-lg border-b-2',
                            isActive('lk.act.create')
                                ? 'text-blue-600 border-blue-600  '
                                : 'border-transparent hover:text-gray-600 hover:border-gray-300 '
                        ]">
                        Счёт/Акт
                        </Link>
                    </li>
                    <li class="me-2">
                        <Link :href="route('lk.sbp.create')" :class="[
                            'inline-block p-4 rounded-t-lg border-b-2',
                            isActive('lk.sbp.create')
                                ? 'text-blue-600 border-blue-600  '
                                : 'border-transparent hover:text-gray-600 hover:border-gray-300 '
                        ]">
                        Платёж СБП
                        </Link>
                    </li>
                    <li class=" ml-auto">
                        <Link :href="route('lk.overwork.create')" :class="[
                            'inline-block p-4 rounded-t-lg border-b-2',
                            isActive('lk.overwork.create')
                                ? 'text-blue-600 border-blue-600  '
                                : 'border-transparent hover:text-gray-600 hover:border-gray-300 '
                        ]">
                        Переработки
                        </Link>
                    </li>

                </ul>
            </div>
            <slot />
        </div>
    </BaseLayout>
</template>

<script>
import BaseLayout from './BaseLayout.vue';
import { usePage } from '@inertiajs/vue3';
import { route } from 'ziggy-js';

export default {
    name: "LkLayout",
    components: { BaseLayout },
    methods: {
        isActive(routeName) {
            const page = usePage();
            const currentRoute = page.props.ziggy.location;
            const targetRoute = route(routeName);

            return currentRoute === targetRoute;
        }
    },
};
</script>