<script lang="ts" setup>
  import {defineProps} from 'vue'
  import {useRoute} from 'vue-router'
  import {Items} from "../../../store/supplierItems/supplierItem/getters";

  const props = defineProps<{items:Items}>()

  const route = useRoute()
  const fields
      = [
    {
      label: 'N'
    },
    {
      label: 'N+1'
    },
    {
      label: 'BS'
    },

    {
      create: false,
      filter: true,
      label: 'Composant',
      name: 'composant',
      options: [{text: 'aaaaa', value: 'aaaaa'}, {text: 'bbbb', value: 'bbbb'}],
      sort: true,
      type: 'select',
      update: false
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
      update: false,
    },
    {
      label: 'Split',
      name: 'split',
      sort: true,
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
      label: 'Quantité Confimrée',
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
    }
  ]




</script>

<template>
  <AppCollectionTable :id="route.name" :fields="fields" :items="items" create pagination>
    <template #split="{item}">
      <td><AppBtnSplit :item="item"/></td>
    </template>
  </AppCollectionTable>
</template>

