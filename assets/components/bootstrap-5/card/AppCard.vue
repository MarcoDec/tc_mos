<script lang="ts" setup>
    import {defineProps, withDefaults} from 'vue'

    withDefaults(defineProps<{titleTag?: string, title: string}>(), {titleTag: 'h2'})
</script>

<template>
    <div class="card">
        <component :is="titleTag" class="card-title">
            {{ title }}
        </component>
        <div class="card-body">
            <slot/>
        </div>
    </div>
</template>
