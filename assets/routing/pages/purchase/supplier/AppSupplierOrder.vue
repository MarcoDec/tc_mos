<script lang="ts" setup>
import type {TableField, TableItem} from '../../../../types/app-collection-table'
import {defineProps} from 'vue'
import {useRoute} from 'vue-router'

defineProps<{fields: TableField[], icon: string, title: string}>()
const route = useRoute()

const items: TableItem[] = [
  {
    delete: true,
    composant: 'composant',
    produit: 'produit',
    type: 'type',
    ref: 'ref',
    quantiteS: 'quantiteS',
    date: 'date',
    quantite: 'quantite',
    etat: 'etat',
    texte: 'texte',
    compagnie: 'compagnie',
    type: null,
    update: false
  },
  {
    delete: true,
    composant: 'composant',
    produit: 'produit',
    type: 'type',
    ref: 'ref',
    quantiteS: 'quantiteS',
    date: 'date',
    quantite: 'quantite',
    etat: 'etat',
    texte: 'texte',
    compagnie: 'compagnie',
    type: null,
    update: false
  },
]

</script>

<template>
  <h1>
    <Fa :icon="icon"/>
    {{ title }}
  </h1>
  <AppCollectionTable :id="route.name" :fields="fields" :items="items" create pagination/>
</template>
