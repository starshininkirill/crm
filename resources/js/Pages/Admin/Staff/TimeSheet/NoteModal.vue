<template>
    <div >
        <h2 class="text-2xl font-semibold mb-4">Примечание для {{ user.full_name }}</h2>
        <form @submit.prevent="saveNote">
            <textarea 
                v-model="content" 
                class="w-full h-40 border rounded-md p-2 focus:ring-blue-500 focus:border-blue-500"
                placeholder="Введите текст примечания..."
            ></textarea>
            <div class="flex justify-end mt-4 gap-2">
                <div v-if="note" type="submit" class="btn !bg-red-500" :disabled="loading" @click="deleteNote">Удалить</div>
                <button type="submit" class="btn" :disabled="loading">Сохранить</button>
            </div>
        </form>
    </div>
</template>

<script>
import axios from 'axios';
import { route } from 'ziggy-js';

export default {
    name: 'NoteModal',
    props: {
        user: {
            type: Object,
            required: true,
        },
        date: {
            type: String,
            required: true,
        },
        note: {
            type: Object,
            default: null,
        },
    },
    emits: ['close', 'note-saved'],
    data() {
        return {
            loading: false,
            content: this.note?.content ?? '',
        };
    },
    methods: {
        saveNote() {
            this.loading = true;
            const url = route('admin.time-sheet.note.store');
            
            axios.post(url, {
                content: this.content,
                user_id: this.user.id,
                date: this.date,
            })
            .then(response => {
                this.$emit('note-saved', response.data.note);
                this.$emit('close');
            })
            .catch(error => {
                console.error("Ошибка при сохранении примечания:", error);
            })
            .finally(() => {
                this.loading = false;
            });
        },
        deleteNote() {
            this.loading = true;
            const url = route('admin.time-sheet.note.destroy', { note: this.note.id });
            axios.delete(url)
                .then(response => {
                    this.$emit('note-deleted', response.data.note);
                    this.$emit('close');
                })
                .catch(error => {
                    console.error("Ошибка при удалении примечания:", error);
                })
                .finally(() => {
                    this.loading = false;
                });
        }
    },
};
</script> 