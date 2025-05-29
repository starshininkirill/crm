<template>
    <div class="grow w-full py-8 px-2">
        <div class="flex gap-3 mb-4 border-b">
            <AdminSubnavLink :href="route('admin.document-generator.index')" :route="'admin.document-generator.index'">
                Документы
            </AdminSubnavLink>
            <AdminSubnavLink :href="route('admin.document-generator.nums')" :route="'admin.document-generator.nums'">
                Нумератор
            </AdminSubnavLink>
        </div>
        <slot />
    </div>
</template>

<script>
import AdminLayout from '../../../Layouts/AdminLayout.vue';
import AdminSubnavLink from '../../../Components/AdminSubnavLink.vue';

export default {
    name: "DocumentTemplateLayout",
    components: { AdminSubnavLink, AdminLayout },
    layout: AdminLayout,
};
</script>