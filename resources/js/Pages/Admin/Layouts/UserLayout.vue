<template>
    <div class="grow w-full py-8 px-2">
        <div class="flex gap-3 mb-4 border-b">
            <AdminSubnavLink :href="route('admin.user.index')" :route="'admin.user.index'">
                Сотрудники
            </AdminSubnavLink>
            <AdminSubnavLink :href="route('admin.position.index')" :route="'admin.position.index'">
                Должности
            </AdminSubnavLink>
            <AdminSubnavLink :href="route('admin.employment-type.index')" :route="'admin.employment-type.index'">
                Типы устройства
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