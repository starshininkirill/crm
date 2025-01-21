<template>
    <AdminLayout>
        <div class="grow w-full py-8 px-2">
            <div class="flex gap-3 mb-4 border-b">
                <AdminSubnavLink :href="route('admin.service.category.index')" :route="'admin.service.category.index'">
                    Категории Услуг
                </AdminSubnavLink>
                <AdminSubnavLink :href="route('admin.service.index')" :route="'admin.service.index'">
                    Все Услуги
                </AdminSubnavLink>
                <AdminSubnavLink :href="route('admin.service.create')" :route="'admin.service.create'">
                    Создать Услугу
                </AdminSubnavLink>
            </div>
            <slot />
        </div>
    </AdminLayout>
</template>

<script>
import AdminLayout from '../../../Layouts/AdminLayout.vue';
import AdminSubnavLink from '../../../Components/AdminSubnavLink.vue';

export default {
    name: "ServiceLayout",
    components: { AdminLayout, AdminSubnavLink },
};
</script>