<template>
    <div class="fixed bottom-4 right-4 z-50">
        <!-- Кнопка знака вопроса -->
        <button @click="toggleLegend"
            class="w-12 h-12 rounded-full bg-blue-500 text-white shadow-lg hover:bg-blue-600 transition-colors duration-200 flex items-center justify-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
        </button>

        <!-- Легенда статусов -->
        <div v-if="showLegend"
            class="absolute bottom-16 right-0 bg-white p-4 rounded-lg shadow-xl border border-gray-200 w-80">
            <!-- Кнопка закрытия -->
            <button @click="toggleLegend" class="absolute top-2 right-2 text-gray-500 hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                    <path fill-rule="evenodd"
                        d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                        clip-rule="evenodd" />
                </svg>
            </button>

            <h3 class="font-semibold text-lg mb-3">Статусы сотрудников</h3>

            <div class="space-y-2">
                <div class="flex items-center">
                    <div class="w-4 h-4 rounded-full bg-red-500 mr-2 flex-shrink-0"></div>
                    <span>Опоздание</span>
                </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 rounded-full bg-cyan-400 mr-2 flex-shrink-0"></div>
                    <span>Больничный/ВЗС</span>
                </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 rounded-full bg-cyan-500 mr-2 flex-shrink-0"></div>
                    <span>Отпуск</span>
                </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 rounded-full bg-orange-400 mr-2 flex-shrink-0"></div>
                    <span>Работа из дома/неполный день</span>
                </div>
                <div class="flex items-center">
                    <div class="w-4 h-4 rounded-full bg-gray-400 mr-2 flex-shrink-0"></div>
                    <span>Выходной день</span>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            showLegend: false
        };
    },
    methods: {
        toggleLegend() {
            this.showLegend = !this.showLegend;
        }
    }
};
</script>
