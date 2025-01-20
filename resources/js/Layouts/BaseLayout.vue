<template>
    <div class="flex flex-col min-h-screen m-0">
        <span v-if="showSuccess"
            class="fixed bottom-5 right-5 max-w-xs w-full bg-green-600 text-white p-4 rounded-lg shadow-lg flex items-center space-x-2 transform transition-opacity duration-300 opacity-100">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            <span>{{ successMessage }}</span>
        </span>
        <Header />
        <main class="h-full grow flex">
            <slot />
        </main>
        <Footer />
    </div>
</template>

<script>
import Header from './Header.vue';
import Footer from './Footer.vue';

export default {
    name: "BaseLayout",
    components: { Header, Footer },
    data() {
        return {
            showSuccess: !!this.$page.props.success,
            successMessage: this.$page.props.success,
        };
    },
    watch: {
        '$page.props.success'(newMessage) {
            if (newMessage) {
                this.successMessage = newMessage;
                this.showSuccess = true;
                setTimeout(() => {
                    this.showSuccess = false;
                }, 5000);
            }
        }
    }
};
</script>