<template>
    <AdminLayout>
        <div class="grow w-full py-8 px-2">
            <div class="flex gap-3 mb-4 border-b">
                <AdminSubnavLink :href="route('admin.organization.index')" :route="'admin.organization.index'">
                    Все организации
                </AdminSubnavLink>
                <AdminSubnavLink :href="route('admin.organization.create')" :route="'admin.organization.create'">
                    Создать организацию
                </AdminSubnavLink>
                <AdminSubnavLink :href="route('admin.organization.document-template.index')" :route="'admin.organization.document-template.index'">
                    Шаблоны документов
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
    name: "ContractLayout",
    components: { AdminLayout, AdminSubnavLink },
};
</script>