<template>
    <DepartmentLayout>
    <Head title="Отдел" />
    <div class="contract-page-wrapper flex flex-col">
        <h1 class="text-4xl font-semibold mb-6">{{ department.name }}</h1>

        <template v-if="parent">
            <h2 class="text-4xl mb-5 font-semibold">Родительский отдел</h2>
            <div class="mb-7">
                <Link :href="route('admin.department.show', { department: parent.id })" class=" text-lg text-blue-600">
                {{ department.parent.name }}
                </Link>
            </div>
        </template>

        <template v-if="childDepartments.length">
            <h2 class="text-4xl mb-5 font-semibold">Подотделы</h2>
            <div class="flex flex-col gap-1 mb-7">
                <Link v-for="childDepartment in childDepartments" :key="childDepartment.id"
                    :href="route('admin.department.show', { department: childDepartment.id })"
                    class=" text-lg text-blue-600">
                {{ childDepartment.name }}
                </Link>
            </div>
        </template>

        <template v-if="users.length">
            <h2 class="text-4xl mb-5 font-semibold">Сотрудники</h2>
            <div class="flex flex-col gap-2">
                <Link v-for="user in users" :key="user.id" :href="route('admin.user.show', { user: user.id })"
                    class=" border-b pb-2 flex flex-col gap-1">
                <div class=" font-semibold text-xl">
                    {{ user.first_name }}
                    {{ user.last_name }}
                </div>
                <div>
                    {{ user.email }}
                </div>
                <div v-if="user.position">
                    Должность: {{ user.position.name }}
                </div>
                </Link>
            </div>
        </template>

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
        department: {
            type: Array,
        },
        parent: {
            type: Object,
        },
        childDepartments: {
            type: Array,
        },
        users: {
            type: Array,
        }
    },
}


</script>