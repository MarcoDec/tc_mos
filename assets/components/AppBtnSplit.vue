<script  setup>
    import {computed, defineProps} from 'vue'
    import AppCollectionTableSplit from '../router/pages/purchase/order-supplier/AppCollectionTableSplit.vue'

    const props = defineProps({
        item: {required: true, type: Object}
    })
    const modalId = computed(() => `split-${props.item.id}`)
    const target = computed(() => `#${modalId.value}`)
    const title = ''
</script>

<template>
    <AppBtn variant="primary" :item="item" data-bs-toggle="modal" :data-bs-target="target">
        <Fa icon="share-alt-square"/>
    </AppBtn>
    <AppModal :id="modalId" :title="title">
        <AppCollectionTableSplit :item="item"/>
    </AppModal>
</template>
