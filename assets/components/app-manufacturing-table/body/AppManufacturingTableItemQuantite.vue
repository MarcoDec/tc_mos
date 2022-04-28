<script lang="ts" setup>
    import type {TableField, TableItem} from '../../../types/app-manufacturing-table'
    import {computed, defineProps, ref} from 'vue'

    const props = defineProps<{field: TableField, item: TableItem}>()
    const value= ref( props.item[props.field.name])
    const e = "La quantité minimale de production est "+ props.item.quantiteMin + " !"

    
</script>

<template>
    <td v-if="value>=item.quantiteMin">
      <AppInputGuesser :field="field" v-model="value" no-label/>
    </td>
    <td v-else class="bg-warning" style="display: flex;">
       <span><Fa icon="exclamation-triangle" :title="e" style="color: red; margin-right: 10px; position: relative; top: 5px; left: 5px;"/> </span> 
       <span><AppInputGuesser :field="field" v-model="value" no-label/></span> 
    </td>
</template>
