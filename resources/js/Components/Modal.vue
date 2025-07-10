<template>
    <div class="modal-wrapper" v-if="open" @click.self="closeModal">
        <div class="modal" :class="modalClass">
            <slot></slot>
            <button class="close-modal" @click="closeModal">
                x
            </button>
        </div>
    </div>
</template>

<script>
export default {
    name: 'Modal',
    props: {
        open: {
            type: Boolean,
            required: true
        },
        modalClass: {
            type: [String, Array, Object],
            default: null
        }
    },
    emits: ['close'],
    methods: {
        closeModal() {
            this.$emit('close');
        }
    }
}
</script>