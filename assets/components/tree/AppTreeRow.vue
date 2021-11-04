<script lang="ts" setup>
    import {defineProps, ref} from 'vue'
    import type {TreeItem} from '../../types/tree'

    defineProps<{item: TreeItem}>()

    const selected = ref<TreeItem | null>(null)

    function input(item: PointerEvent | TreeItem): void {
        if (!(item instanceof PointerEvent))
            selected.value = item
    }
</script>

<template>
    <AppRow>
        <AppTree :item="item" class="col" @click="input"/>
        <AppCard class="bg-blue col">
            <h2>{{ selected?.label }}</h2>
            <slot/>
        </AppCard>
    </AppRow>
</template>
