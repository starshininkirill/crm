<template>
    <div class="grow w-full py-8 px-2">
        <div class="flex gap-3 mb-4 border-b">
            <AdminSubnavLink :href="route('admin.settings.index')" :route="'admin.settings.index'">
                Основные настройки
            </AdminSubnavLink>
            <AdminSubnavLink :href="route('admin.settings.calendar')" :route="'admin.settings.calendar'">
                Календарь рабочих дней
            </AdminSubnavLink>
            <AdminSubnavLink :href="route('admin.settings.finance-week')" :route="'admin.settings.finance-week'">
                Настройка рабочих недель
            </AdminSubnavLink>
            <AdminSubnavLink :href="route('admin.settings.time-check')" :route="'admin.settings.time-check'">
                Настройки Time Check
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
    components: { AdminSubnavLink },
    layout: AdminLayout,
};
</script>