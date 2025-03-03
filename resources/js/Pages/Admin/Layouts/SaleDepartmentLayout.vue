<template>
    <div class="grow w-full py-8 px-2">
        <div class="flex gap-3 mb-4 border-b">
            <AdminSubnavLink :href="route('admin.sale-department.index')" :route="'admin.sale-department.index'">
                Главная
            </AdminSubnavLink>
            <AdminSubnavLink :href="route('admin.sale-department.calls')" :route="'admin.sale-department.calls'">
                Отчёт по разговорам
            </AdminSubnavLink>
            <AdminSubnavLink :href="route('admin.sale-department.user-report')"
                :route="'admin.sale-department.user-report'">
                Отчёт по менеджерам
            </AdminSubnavLink>
            <AdminSubnavLink :href="route('admin.sale-department.plans-settings')"
                :route="'admin.sale-department.plans-settings'">
                Настройка планов
            </AdminSubnavLink>
            <AdminSubnavLink :href="route('admin.sale-department.t2-settings')"
                :route="'admin.sale-department.t2-settings'">
                Т2 API
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
    components: {  AdminSubnavLink },
    layout: AdminLayout,
};
</script>