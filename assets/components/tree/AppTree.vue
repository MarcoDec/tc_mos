<script lang="ts" setup>
    import type {ReadTreeItem, TreeItem} from '../../types/tree'
    import {computed, defineEmits, defineProps, ref} from 'vue'

    const emit = defineEmits<(e: 'click', item: ReadTreeItem) => void>()
    const props = defineProps<{item: TreeItem}>()

    const EMPTY = 0

    const opened = ref(false)

    const hasChildren = computed(() => typeof props.item.children !== 'undefined' && props.item.children.length > EMPTY)
    const itemTag = computed(() => (hasChildren.value ? 'AppTreeClickableItem' : 'AppTreeItem'))
    const showChildren = computed(() => hasChildren.value && opened.value)

    function click(e: boolean): void {
        opened.value = e
        emit('click', props.item)
    }

    function childClick(item: ReadTreeItem): void {
        emit('click', item)
    }
</script>

<template>
    <div>
        <component :is="itemTag" :item="item" @click="click"/>
        <template v-if="showChildren">
            <AppTree v-for="child in item.children" :key="child.id" :item="child" class="ms-4" @click="childClick"/>
        </template>
    </div>
</template>
