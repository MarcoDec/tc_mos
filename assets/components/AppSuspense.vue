<script setup>
    import {computed} from 'vue'

    const props = defineProps({variant: {default: 'dark', type: String}})
    const text = computed(() => `text-${props.variant}`)
</script>

<template>
    <Suspense>
        <template #fallback>
            <span :class="text" class="spinner-border" role="status"/>
        </template>
        <slot/>
    </Suspense>
</template>
