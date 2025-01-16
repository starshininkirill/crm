<template>
    <AdminLayout>
        <div class="grow w-full py-8 px-2">
            <div class="flex gap-3 mb-4 border-b">
                <AdminSubnavLink :href="route('admin.payment.index')" :route="'admin.payment.index'">
                    Все Платежи
                </AdminSubnavLink>
                <AdminSubnavLink :href="route('admin.payment.unsorted')" :route="'admin.payment.unsorted'">
                    Неразобранные платежи (РС)
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