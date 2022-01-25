<script lang="ts" setup>
import type {TableField, TableItem} from '../../../../types/app-collection-table'
import {defineProps,onMounted} from 'vue'
import {useRoute} from 'vue-router'
import {useNamespacedActions,useNamespacedGetters} from 'vuex-composition-helpers'
import { ActionTypes} from "../../../../store/achat/settings";
import type {Actions, Getters} from '../../../../store/achat/settings'
defineProps<{ fields: TableField[], icon: string, title: string }>()
const route = useRoute()
const {ids: it} = useNamespacedGetters<Getters>('settings', ['ids'])
const fetch = useNamespacedActions<Actions>('settings', [ActionTypes.FETCH_SETTING])[ActionTypes.FETCH_SETTING]
const items: TableItem[] =
    [
      {
        delete: true,
        name: 'bbb',
        valeur: 'vvv',
        update: true
      },
      {
        delete: true,
        name: 'aaaa',
        valeur: 'xxxx',
        update: true
      }
    ]
console.log('iiii',items.value)
onMounted(async () => {
  await fetch()
})
</script>

<template>
  <h1>
    <Fa :icon="icon"/>
    {{ title }}
  </h1>
  <AppCollectionTable :id="route.name" :fields="fields" :items="items" create pagination/>
</template>
