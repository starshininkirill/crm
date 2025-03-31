<template>
    <DepartmentLayout>

        <Head title="Отделы" />
        <div class="contract-page-wrapper flex flex-col">
            <h1 class="text-4xl font-semibold mb-6">Отделы</h1>
            
            <h2 v-if="!departments.length" class="text-xl">Отделы не найдены</h2>

            <div v-if="departments.length" class="flex flex-col gap-3">
                <div v-for="department in departments" :key="department.id" class="p-4 text-xl border">
                    <Link :href="route('admin.department.show', { department: department.id })" class="text-xl">
                    {{ department.name }}
                    </Link>
                    <template v-if="department.childsDepartments.length">
                        <div class="font-bold text-lg mb-1 mt-3 pl-3">
                            Подотделы
                        </div>
                        <ul class="flex flex-col gap-1 pl-3">
                            <li v-for="childDepartment in department.childsDepartments" :key="childDepartment.id"
                                class=" text-lg list-disc list-inside">
                                <Link :href="route('admin.department.show', { department: childDepartment.id })">
                                {{ childDepartment.name }}
                                </Link>
                            </li>
                        </ul>
                    </template>
                </div>
            </div>
        </div>
    </DepartmentLayout>
</template>

<script>
import { Head } from '@inertiajs/vue3';
import DepartmentLayout from '../Layouts/DepartmentLayout.vue';

export default {
    components: {
        Head,
        DepartmentLayout
    },
    props: {
        departments: {
            type: Array,
        },
    },
}


</script>