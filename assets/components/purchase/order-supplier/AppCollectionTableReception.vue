<script lang="ts" setup>

import {
  useNamespacedActions,
  useNamespacedGetters,useNamespacedState
} from 'vuex-composition-helpers'
import {useRoute} from 'vue-router'
import {onMounted} from 'vue'
import type {Actions,Getters} from '../../../store/supplierItems'
import type {State} from '../../../store/supplierItems/supplierItem'

const route = useRoute()
const fields: FormField[] =
    [
      {
        label: 'Composant',
      },
      {
        label: 'Quantité Confirmée',
      },
      {
        label: 'Quantité Reçue',
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

    ]

const fetchItem = useNamespacedActions<Actions>('supplierItems', ['fetchItem'])['fetchItem']
const {items} = useNamespacedGetters<Getters>('supplierItems', ['items'])
//const items = useNamespacedState<State>('supplierItems', ['list']).list

onMounted(async () => {
  await fetchItem()
})

</script>

<template>
  <AppCollectionTable :id="route.name" :fields="fields" :items="items" create pagination/>
</template>

