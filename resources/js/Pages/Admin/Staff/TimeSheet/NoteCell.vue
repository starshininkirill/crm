<template>
    <td 
        class="px-2 py-3 border-r cursor-pointer relative group hover:bg-gray-200" 
        :class="{ 'bg-yellow-200': note }"
        @click.stop="$emit('openModal')"
    >
        <span class="truncate block">{{ note ? 'Есть' : 'Нет' }}</span>

        <div v-if="note?.content" class="absolute hidden group-hover:block z-20 bg-white shadow-lg rounded-md p-2 border border-gray-200 min-w-[250px] max-w-[350px] left-full ml-2 top-0 pointer-events-none text-left">
            <p class="text-sm text-gray-700 whitespace-pre-wrap">{{ note.content }}</p>
        </div>
    </td>
</template>

<script>
export default {
    name: 'NoteCell',
    props: {
        note: {
            type: Object,
            default: null,
        },
    },
    emits: ['openModal']
};
</script> 