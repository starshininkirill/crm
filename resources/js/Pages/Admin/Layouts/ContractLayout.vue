<template>
    <div class="grow w-full py-8 px-2" id="contract-page">
        <div class="flex gap-3 mb-4 border-b">
            <AdminSubnavLink :href="route('admin.contract.index')" :route="'admin.contract.index'">
                Все Договоры
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