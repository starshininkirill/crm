<template>
    <div class="grow w-full py-8 px-2">
        <div class="flex gap-3 mb-4 border-b">
            <AdminSubnavLink :href="route('admin.department.index')" :route="'admin.department.index'">
                Все Отделы
            </AdminSubnavLink>
            <AdminSubnavLink :href="route('admin.department.create')" :route="'admin.department.create'">
                Создать Отдел
            </AdminSubnavLink>
        </div>
        <slot />
    </div>
</template>

<script>
import AdminLayout from '../../../Layouts/AdminLayout.vue';
import AdminSubnavLink from '../../../Components/AdminSubnavLink.vue';

export default {
    name: "UserLayout",
    components: { AdminLayout, AdminSubnavLink },
    layout: AdminLayout,
};
</script>