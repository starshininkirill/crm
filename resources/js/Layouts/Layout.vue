<template>
    <BaseLayout>
        <slot />
    </BaseLayout>
</template>

<script>
import BaseLayout from './BaseLayout.vue';

export default {
    name: "Layout",
    components: { BaseLayout },
};
</script>