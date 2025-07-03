<template>
    <div class="grow w-full py-8 px-2">
        <div class="flex gap-3 mb-4 border-b">
            <AdminSubnavLink :href="route('admin.projects-department.index')"
                :route="'admin.projects-department.index'">
                Главная
            </AdminSubnavLink>
            <AdminSubnavLink :href="route('admin.projects-department.report')"
                :route="'admin.projects-department.report'">
                Отчёт по сотрудникам
            </AdminSubnavLink>
            <AdminSubnavLink :href="route('admin.projects-department.plans-settings')"
                :route="'admin.projects-department.plans-settings'">
                Настройки планов
            </AdminSubnavLink>
        </div>
        <slot />
    </div>
</template>

<script>
import AdminLayout from '../../../Layouts/AdminLayout.vue';
import AdminSubnavLink from '../../../Components/AdminSubnavLink.vue';

export default {
    name: "ProjectsLayout",
    components: { AdminSubnavLink },
    layout: AdminLayout,
};
</script>