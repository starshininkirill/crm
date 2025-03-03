<template>
    <div class="grow w-full py-8 px-2">
        <div class="flex gap-3 mb-4 border-b">
            <AdminSubnavLink :href="route('admin.organization.index')" :route="'admin.organization.index'">
                Все организации
            </AdminSubnavLink>
            <AdminSubnavLink :href="route('admin.organization.document-template.index')"
                :route="'admin.organization.document-template.index'">
                Шаблоны документов
            </AdminSubnavLink>
            <AdminSubnavLink :href="route('admin.organization.document-template.attach')"
                :route="'admin.organization.document-template.attach'">
                Привязать шаблон
            </AdminSubnavLink>
        </div>
        <slot />
    </div>
</template>

<script>
import AdminLayout from '../../../Layouts/AdminLayout.vue';
import AdminSubnavLink from '../../../Components/AdminSubnavLink.vue';

export default {
    name: "ContractLayout",
    components: { AdminSubnavLink },
    layout: AdminLayout,
};
</script>