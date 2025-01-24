<template>
    <div>
        <div class="text-xl font-semibold mb-3">{{ title }}</div>
        <form @submit.prevent="handleSubmit" class="flex flex-col gap-2 cursor-pointer">
            <label v-for="category in categories" :key="category.id" class="flex items-center gap-2">
                <input type="checkbox" :value="category.id" v-model="form.value" class="cursor-pointer" />
                <span class="cursor-pointer">
                    {{ category.name }}
                </span>
            </label>

            <input type="hidden" name="name" :value="form.name" />
            <button type="submit" class="btn mt-3">Изменить</button>
        </form>
    </div>
</template>

<script>
import { useForm } from "@inertiajs/vue3";

export default {
    props: {
        title: {
            type: String,
            required: true,
        },
        categories: {
            type: Array,
            required: true,
        },
        optionObject: {
            type: Object,
            required: true,
        },
        name: {
            type: String,
            required: true,
        },
    },
    setup(props) {
        const selectedCategories = props.optionObject?.value ? JSON.parse(props.optionObject.value) : [];        

        const form = useForm({
            'name': props.name,
            'value': selectedCategories,
        });

        const handleSubmit = () => {
            if (!props.optionObject || !props.optionObject?.value) {
                form.post(route('option.store'));
            } else {
                form.put(route('option.update', {option : props.optionObject.id}));
            }
        };

        return {
            handleSubmit,
            form
        };
    },
};
</script>