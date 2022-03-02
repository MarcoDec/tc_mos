<script lang="ts" setup>
    import type {Items} from '../../../store/supplierItems/supplierItem/getters'
    import {defineProps} from 'vue'

    defineProps<{value: number, item: Items}>()
</script>

<template>
    <tr class="text-center">
        <td/>
        <td>
            <span>Total confirmé:{{ item.quantite }}</span>
        </td>
        <td>
            <span>Reste: {{ value }}</span>
        </td>
    </tr>
</template>
