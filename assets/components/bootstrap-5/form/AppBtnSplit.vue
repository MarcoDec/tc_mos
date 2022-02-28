<script lang="ts" setup>
  import {computed, defineProps} from "vue";
  import {Items} from "../../../store/supplierItems/supplierItem/getters"
  const p = defineProps<{item:Items}>()
  const modalId = computed(()=> `split-${p.item.id}`)
  const target = computed(()=> `#${modalId.value}`)
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
