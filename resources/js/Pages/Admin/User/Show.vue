<template>
    <UserLayout>

        <Head :title="user.full_name" />
        <div class="contract-page-wrapper flex flex-col">
            <h1 class="text-4xl font-semibold mb-6">Сотрудник: {{ user.full_name }}</h1>


            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Контактная информация -->
                <div class="bg-white rounded-xl shadow-md p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-medium text-gray-800">Контактная информация</h2>
                    </div>

                    <div class="space-y-4">
                        <div class="flex justify-between border-b pb-1">
                            <div class="text-sm text-gray-500 font-medium">Фамилия</div>
                            <div class="text-gray-800 text-right font-semibold">{{ user.last_name }}</div>
                        </div>
                        <div class="flex justify-between border-b pb-1">
                            <div class="text-sm text-gray-500 font-medium">Имя</div>
                            <div class="text-gray-800 text-right font-semibold">{{ user.first_name }}</div>
                        </div>
                        <div class="flex justify-between border-b pb-1">
                            <div class="text-sm text-gray-500 font-medium">Отчество</div>
                            <div class="text-gray-800 text-right font-semibold">{{ user.surname }}</div>
                        </div>
                        <div class="flex justify-between border-b pb-1">
                            <div class="text-sm text-gray-500 font-medium">Bitrix id</div>
                            <div class="text-gray-800 text-right font-semibold">{{ user.bitrix_id }}</div>
                        </div>
                        <div class="flex justify-between border-b pb-1">
                            <div class="text-sm text-gray-500 font-medium">Контактный e-mail</div>
                            <div class="text-gray-800 text-right font-semibold">{{ user.email }}</div>
                        </div>
                        <div class="flex justify-between border-b pb-1">
                            <div class="text-sm text-gray-500 font-medium">Личный телефон</div>
                            <div class="text-gray-800 text-right font-semibold">{{ user.phone ?? 'Не заполнено' }}</div>
                        </div>
                        <div class="flex justify-between border-b pb-1">
                            <div class="text-sm text-gray-500 font-medium">Рабочий телефон</div>
                            <div class="text-gray-800 text-right font-semibold">{{ user.work_phone }}</div>
                        </div>
                        <div class="flex justify-between border-b pb-1">
                            <div class="text-sm text-gray-500 font-medium">Дата принятия на работу</div>
                            <div class="text-gray-800 text-right font-semibold">{{ user.created_at }}</div>
                        </div>
                        <div class="flex justify-between border-b pb-1">
                            <div class="text-sm text-gray-500 font-medium">Отдел</div>
                            <div class="text-blue-600 text-right font-semibold">
                                <Link :href="route('admin.department.show', { department: user.department.id })">
                                {{ user.department.name }}
                                </Link>
                            </div>
                        </div>
                        <div class="flex justify-between border-b pb-1">
                            <div class="text-sm text-gray-500 font-medium">Должность</div>
                            <div class="text-gray-800 text-right font-semibold">{{ user.position.name }}</div>
                        </div>
                    </div>
                </div>

                <!-- Платежная информация -->
                <div class="bg-white rounded-xl shadow-md p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-medium text-gray-800">Платежная информация</h2>
                    </div>

                    <div class="space-y-4">
                        <div class="flex justify-between border-b pb-1">
                            <div class="text-sm text-gray-500 font-medium">Тип устройства</div>
                            <div class="text-gray-800 text-right font-semibold">
                                {{ user?.employmentDetail?.employment_type.name }}
                            </div>
                        </div>
                        <div class="flex justify-between border-b pb-1">
                            <div class="text-sm text-gray-500 font-medium">Компенсация за налог (%)</div>
                            <div class="text-gray-800 text-right font-semibold">
                                {{ user.employmentDetail?.employment_type.compensation }}
                            </div>
                        </div>
                        <div class="flex justify-between border-b pb-1">
                            <div class="text-sm text-gray-500 font-medium">Расчётный счёт</div>
                            <div class="text-gray-800 text-right font-semibold">
                                {{ user.employmentDetail?.payment_account }}
                            </div>
                        </div>
                        <div v-for="row in user.employmentDetail?.details" class="flex justify-between border-b pb-1">
                            <div class="text-sm text-gray-500 font-medium">
                                {{ row.readName }}
                            </div>
                            <div class="text-gray-800 text-right font-semibold">
                                {{ row.value }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </UserLayout>
</template>

<script>
import { Head } from '@inertiajs/vue3';
import UserLayout from '../Layouts/UserLayout.vue';

export default {
    components: {
        Head,
        UserLayout
    },
    props: {
        user: {
            type: Object,
        },
    },
}


</script>