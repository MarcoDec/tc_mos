<script lang="ts" setup>
    import {computed, defineProps} from 'vue'

    const props = defineProps<{id: string}>()
    const nav = computed(() => `nav-${props.id}`)
</script>

<template>
    <div :id="id">
        <ul :id="nav" class="nav"/>
        <div class="tab-content">
            <slot/>
        </div>
    </div>
</template>
