<script setup>
    import {defineProps} from 'vue'

    defineProps({item: {required: true, type: Object}, value: {required: true, type: Number}})
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
