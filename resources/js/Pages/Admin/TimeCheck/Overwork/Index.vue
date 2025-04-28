<template>
    <TimeCheckLayout>

        <Head title="Переработки" />
        <div class="contract-page-wrapper flex flex-col">
            <h1 class="text-4xl font-semibold mb-6">Переработки</h1>
        </div>

        <h2 v-if="!overworks.length" class="text-2xl font-semibold">
            Переработок не найдено
        </h2>
        <table v-else
            class="shadow-md overflow-hidden rounded-md sm:rounded-lg w-full text-sm text-left rtl:text-right text-gray-500 ">
            <thead class="thead">
                <tr>
                    <th scope="col" class="px-6 border-r py-3 w-32">
                        Дата
                    </th>
                    <th scope="col" class="px-6 border-r py-3 w-16">
                        Часы
                    </th>
                    <th scope="col" class="px-6 border-r py-3">
                        Сотрудник
                    </th>
                    <th scope="col" class="px-6 border-r py-3 flex-grow">
                        Задачи
                    </th>
                    <th scope="col" class="px-6 border-r py-3 flex-grow">
                        Описание
                    </th>
                    <th scope="col" class="px-6 border-r py-3">
                        Действия
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="overwork in overworks" :key="overwork.id" class="table-row">
                    <td class="px-6 border-r py-4 whitespace-nowrap">
                        {{ overwork.date }}
                    </td>
                    <td class="px-6 border-r py-4 whitespace-nowrap">
                        {{ overwork.hours }}
                    </td>
                    <th scope="row" class="px-6 border-r py-4 font-medium text-gray-900 whitespace-nowrap">
                        {{ overwork.user?.full_name }}
                    </th>
                    <td class="px-6 border-r py-4">
                        <div v-if="overwork.links">
                            <a v-for="(link, index) in parseLinks(overwork.links)" :key="index" :href="link"
                                target="_blank" rel="noopener noreferrer" class="block text-blue-500 hover:underline">
                                {{ link }}
                            </a>
                        </div>
                    </td>
                    <td class="px-6 border-r py-4 w-full">
                        {{ overwork.report }}
                    </td>
                    <td class="px-6 border-r py-4 whitespace-nowrap flex gap-3 items-center">
                        <button @click="openModal('accept', overwork)">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="#4CAF50" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="feather feather-check">
                                <path d="M20 6L9 17l-5-5" />
                            </svg>
                        </button>
                        <button @click="openModal('reject', overwork)">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24"
                                fill="none" stroke="#F44336" stroke-width="2" stroke-linecap="round"
                                stroke-linejoin="round" class="feather feather-x">
                                <line x1="18" y1="6" x2="6" y2="18" />
                                <line x1="6" y1="6" x2="18" y2="18" />
                            </svg>
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>

        <Modal :open="isModalOpen" @close="closeModal">
            <h2 class="text-xl font-bold mb-4">{{ selectedOverwork?.user?.full_name }} -
                {{ modalAction === 'accept' ? 'Подтвердить переработку' : 'Отклонить переработку' }}
            </h2>

            <div>
                <label class="label">
                    Комментарий
                </label>
                <textarea v-model="description" placeholder="Введите комментарий..."
                    class="input resize-none h-24"></textarea>
            </div>

            <div class="flex justify-end gap-3">
                <button @click="closeModal" class="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">
                    Отмена
                </button>
                <button @click="submitAction" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    {{ modalAction === 'accept' ? 'Подтвердить' : 'Отклонить' }}
                </button>
            </div>
        </Modal>
    </TimeCheckLayout>
</template>

<script>
import { Head, router } from '@inertiajs/vue3';
import TimeCheckLayout from '../../Layouts/TimeCheckLayout.vue';
import Modal from '../../../../Components/Modal.vue';

export default {
    components: {
        Head,
        TimeCheckLayout,
        Modal
    },
    props: {
        overworks: {
            type: Array,
            required: true,
        },
    },
    data() {
        return {
            isModalOpen: false,
            modalAction: null,
            selectedOverwork: null,
            description: '',
        };
    },
    methods: {
        openModal(action, overwork) {
            this.modalAction = action;
            this.selectedOverwork = overwork;
            this.isModalOpen = true;
            this.description = '';
        },
        closeModal() {
            this.isModalOpen = false;
            this.modalAction = null;
            this.selectedOverwork = null;
            this.description = '';
        },
        submitAction() {
            const routeName = this.modalAction === 'accept'
                ? 'admin.time-check.overwork.accept'
                : 'admin.time-check.overwork.reject';

            router.post(route(routeName, { overwork: this.selectedOverwork.id }), {
                description: this.description,
            });

            this.closeModal();
        },
        parseLinks(links) {
            if (!links) return [];
            return links.split(',').map(link => link.trim());
        },
    },
};
</script>