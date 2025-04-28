<template>
    <div class="grow w-full py-8 px-2">
        <div class="flex gap-3 mb-4 border-b">
            <AdminSubnavLink :href="route('admin.service.index')" :route="'admin.service.index'">
                Все Услуги
            </AdminSubnavLink>
            <AdminSubnavLink :href="route('admin.service.category.index')" :route="'admin.service.category.index'">
                Категории Услуг
            </AdminSubnavLink>
        </div>
        <slot />
    </div>
</template>

<script>
import AdminLayout from '../../../Layouts/AdminLayout.vue';
import AdminSubnavLink from '../../../Components/AdminSubnavLink.vue';

export default {
    name: "ServiceLayout",
    components: { AdminSubnavLink },
    layout: AdminLayout,
};
</script>