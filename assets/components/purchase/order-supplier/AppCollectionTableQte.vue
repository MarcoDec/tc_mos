<script lang="ts" setup>
import {
  useNamespacedActions,
  useNamespacedGetters
} from 'vuex-composition-helpers'
import {useRoute} from 'vue-router'
import {onMounted} from 'vue'
import type {Actions,Getters} from '../../../store/supplierItems'

const route = useRoute()
const fields: FormField[] =
    [
      {
        label: 'Composant',
      },
      {
        label: 'Date Récéption',
      },
      {
        label: 'Status',
      },
      {
        label: 'Commentaire',
      },
      {
        label: 'Controle',
      },
      {
        label: 'Valeur',
      },
    ]

const fetchItem = useNamespacedActions<Actions>('supplierItems', ['fetchItem'])['fetchItem']
const {items} = useNamespacedGetters<Getters>('supplierItems', ['items'])
onMounted(async () => {
  await fetchItem()
})

</script>

<template>
  <AppCollectionTable :id="route.name" :fields="fields" :items="items" create pagination/>
</template>

