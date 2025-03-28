<template>
    <div class="grow w-full py-8 px-2">
        <div class="flex gap-3 mb-4 border-b">
            <AdminSubnavLink :href="route('admin.time-check.index')" :route="'admin.time-check.index'">
                Time Check
            </AdminSubnavLink>
            <AdminSubnavLink :href="route('admin.time-sheet')" :route="'admin.time-sheet'">
                Кадровый табель
            </AdminSubnavLink>
            <AdminSubnavLink :href="route('admin.time-check.overwork')" :route="'admin.time-check.overwork'">
                Переработки
            </AdminSubnavLink>
        </div>
        <slot />
    </div>
</template>

<script>
import AdminLayout from '../../../Layouts/AdminLayout.vue';
import AdminSubnavLink from '../../../Components/AdminSubnavLink.vue';

export default {
    name: "TimeCheckLayout",
    components: { AdminSubnavLink },
    layout: AdminLayout,
};
</script>