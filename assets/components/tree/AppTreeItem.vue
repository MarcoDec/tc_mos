<script lang="ts" setup>
    import {computed, defineProps} from 'vue'
    import type {TreeItem} from '../../types/tree'

    const props = defineProps<{item: TreeItem}>()
    const icon = computed(() => props.item.icon ?? 'folder')
</script>

<template>
    <span>
        <slot>
            <span class="pe-4"/>
        </slot>
        <Fa :icon="icon"/>
        {{ item.code }}-{{ item.name }}
    </span>
</template>
