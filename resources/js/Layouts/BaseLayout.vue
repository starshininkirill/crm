<template>
    <div class="flex flex-col min-h-screen m-0">
        <Header />
        <main>
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
};
</script>