<template>
    <div class="grow w-full py-8 px-2">
        <div class="flex gap-3 mb-4 border-b">
            <AdminSubnavLink :href="route('admin.payment.index')" :route="'admin.payment.index'">
                Все Платежи
            </AdminSubnavLink>
            <AdminSubnavLink :href="route('admin.payment.unsorted')" :route="'admin.payment.unsorted'">
                Неразобранные платежи (РС)
            </AdminSubnavLink>
            <AdminSubnavLink :href="route('admin.payment.unsortedSbp')" :route="'admin.payment.unsortedSbp'">
                Неразобранные платежи (СБП)
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
    components: { AdminLayout, AdminSubnavLink },
    layout: AdminLayout,
};
</script>