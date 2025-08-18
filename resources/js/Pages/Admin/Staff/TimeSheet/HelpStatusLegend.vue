<template>
    <div class="w-fit">
        <h3 class="font-semibold text-lg mb-3">Статусы сотрудников</h3>

        <div class="grid grid-cols-3 gap-1">
            <div class="p-1 rounded bg-cyan-400 text-white text-center text-sm">
                Больничный/ВЗС
            </div>
            <div class="p-1 rounded bg-cyan-500 text-white text-center text-sm">
                Отпуск
            </div>
            <div class="p-1 rounded bg-orange-400 text-white text-center text-sm">
                Работа из дома/неполный день
            </div>
            <div class="p-1 rounded bg-red-500 text-white text-center text-sm">
                Опоздание
            </div>
            <div class="p-1 rounded bg-red-700 text-white text-center text-sm">
                Уволен
            </div>
            <div class="p-1 rounded bg-gray-400 text-white text-center text-sm">
                Выходной день
            </div>
        </div>
    </div>
</template>

<script>
export default {

};
</script>
