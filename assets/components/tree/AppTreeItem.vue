<script lang="ts" setup>
    import {computed, defineProps} from 'vue'
    import type {TreeItem} from '../../types/tree'

    const props = defineProps<{item: TreeItem}>()
    const icon = computed(() => (typeof props.item.icon !== 'undefined' ? props.item.icon : 'folder'))
</script>

<template>
    <span>
        <slot>
            <span class="pe-4"/>
        </slot>
        <Fa :icon="icon"/>
        {{ item.label }}
    </span>
</template>
