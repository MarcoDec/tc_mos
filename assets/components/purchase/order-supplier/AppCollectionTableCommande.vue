<script lang="ts" setup>
import {
  useNamespacedActions,
  useNamespacedState,useNamespacedGetters
} from 'vuex-composition-helpers'
import {useRoute} from 'vue-router'
import {defineProps,onMounted} from 'vue'
import type {Actions, Getters} from '../../../store/supplierItems'



const route = useRoute()
const fields: FormField[] =
    [
      {
        label: 'N',
      },
      {
        create: false,
        filter: true,
        label: 'Composant',
        name: 'composant',
        options: [{text: 'aaaaa', value: 'aaaaa'}, {text: 'bbbb', value: 'bbbb'}],
        sort: false,
        type: 'select',
        update: true
      },
      {
        create: false,
        filter: true,
        label: 'Produit',
        name: 'produit',
        options: [{text: 'aaaaa', value: 'aaaaa'}, {text: 'bbbb', value: 'bbbb'}],
        sort: false,
        type: 'select',
        update: true
      },
      {
        create: true,
        filter: true,
        label: 'Référence Fournisseur',
        name: 'ref',
        sort: true,
        type: 'text',
        update: false
      },
      {
        create: true,
        filter: true,
        label: 'Quantité Souhaitée',
        name: 'quantiteS',
        sort: true,
        type: 'text',
        update: false
      },
      {
        create: true,
        filter: true,
        label: 'Date Souhaitée',
        name: 'date',
        sort: false,
        type: 'date',
        update: false
      },
      {
        create: true,
        filter: true,
        label: 'Quantité',
        name: 'quantite',
        sort: true,
        type: 'text',
        update: false
      },
      {
        create: true,
        filter: true,
        label: 'Date de confirmation',
        name: 'date',
        sort: false,
        type: 'date',
        update: false
      },
      {
        create: true,
        filter: true,
        label: 'Etat',
        name: 'etat',
        sort: true,
        type: 'text',
        update: false
      },
      {
        create: true,
        filter: true,
        label: 'Texte',
        name: 'texte',
        sort: true,
        type: 'text',
        update: false
      },
      {
        create: false,
        filter: true,
        label: 'Compagnie destinataire',
        name: 'compagnie',
        options: [{text: 'aaaaa', value: 'aaaaa'}, {text: 'bbbb', value: 'bbbb'}],
        sort: false,
        type: 'select',
        update: true
      },

    ]

const fetchItem = useNamespacedActions<Actions>('supplierItems', ['fetchItem'])['fetchItem']
const {items} = useNamespacedGetters<Getters>('supplierItems', ['items'])

//const items = useNamespacedState<State>('supplierItems', ['list']).list
console.log('bbbbb',items)
onMounted(async () => {
  await fetchItem()
})
</script>

<template>
    <AppCollectionTable :id="route.name" :fields="fields" :items="items" create pagination/>
</template>

