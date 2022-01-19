<script lang="ts" setup>
    import type {ReadTreeItem, TreeItem} from '../../types/tree'
    import {computed, defineProps, ref} from 'vue'
    import type {FormField} from '../../types/bootstrap-5'

    defineProps<{fields: FormField[], item: TreeItem}>()

    const selected = ref<ReadTreeItem | null>(null)
    const NO_FAMILY = 0
    const hasSelected = computed(() => selected.value !== null && selected.value.id > NO_FAMILY)

    function click(item: ReadTreeItem): void {
        selected.value = item
    }
</script>

<template>
    <AppRow>
        <AppTree :item="item" class="col" @click="click"/>
        <AppTreeStoredForm v-if="hasSelected" :key="selected?.id" :family="selected?.id" :fields="fields" class="col"/>
        <AppTreeForm v-else :fields="fields" class="col"/>
    </AppRow>
</template>
