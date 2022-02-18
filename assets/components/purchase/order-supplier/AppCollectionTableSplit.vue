<script lang="ts" setup>
  import {defineProps, onMounted, ref} from 'vue'
  import {Items} from "../../../store/supplierItems/supplierItem/getters"

  const props = defineProps<{item:Items}>()

  const fields
      = [
    {
      label: 'Quantité confirmée',
      name: 'quantite',
      sort: false,
      type: 'text',
      update: true
    },
    {
      label: 'Date confirmée',
      name: 'date',
      sort: false,
      type: 'date',
      update: true
    },
  ]

  const items = ref<Items[]>([])

  onMounted( () => {
    items.value.push(props.item)
  })

</script>

<template>
  <div>
    <AppRow>
      <AppCol>
        <AppCard>
          <span>Quantité souhaitée: {{item.quantite}}</span>
          <span>Date souhaitée: {{item.date}}</span>
        </AppCard>
      </AppCol>
    </AppRow>
 <div>
   <AppTableSplit :fields="fields" :items="items"/>
 </div>

  </div>
</template>

