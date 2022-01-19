<script lang="ts" setup>
    import type {ReadTreeItem, TreeItem} from '../../types/tree'
    import {computed, defineProps, ref} from 'vue'
    import type {FormField} from '../../types/bootstrap-5'

    const props = defineProps<{fields: FormField[], id: string, item: TreeItem}>()

    const cardId = computed(() => `${props.id}-card`)
    const selected = ref<ReadTreeItem | null>(null)
    const NO_FAMILY = 0
    const hasSelected = computed(() => selected.value !== null && selected.value.id > NO_FAMILY)

    function click(item: ReadTreeItem): void {
        selected.value = item
    }
</script>

<template>
    <AppRow :id="id">
        <AppTree :item="item" class="col" @click="click"/>
        <AppTreeStoredForm
            v-if="hasSelected"
            :id="cardId"
            :key="selected?.id"
            :family="selected?.id"
            :fields="fields"
            class="col"/>
        <AppTreeForm v-else :id="cardId" :fields="fields" class="col"/>
    </AppRow>
</template>
