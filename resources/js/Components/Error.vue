<template>
    <div v-if="$page.props.errors && Object.keys($page.props.errors).length">
        <ul class="flex flex-col gap-1 mb-4">
            <li v-for="(messages, field) in $page.props.errors" :key="field">
                <span v-for="message in messages" :key="message" class="text-xl text-red-400">{{ message }}</span>
            </li>
        </ul>
    </div>
</template>