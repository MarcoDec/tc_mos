<script lang="ts" setup>
    import {computed, defineEmits, defineProps, ref} from 'vue'
    import type {TreeItem} from '../../types/tree'

    const emit = defineEmits<(e: 'click', opened: boolean) => void>()
    defineProps<{item: TreeItem}>()

    const opened = ref(false)
    const chevron = computed(() => `chevron-${opened.value ? 'up' : 'down'}`)

    function click(): void {
        emit('click', opened.value = !opened.value)
    }
</script>

<template>
    <AppTreeItem :item="item" class="pointer" @click="click">
        <Fa :icon="chevron" class="me-2"/>
    </AppTreeItem>
</template>
