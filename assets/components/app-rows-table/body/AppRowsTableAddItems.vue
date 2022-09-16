<script setup>
    import {computed, defineProps} from 'vue'

    const props = defineProps({fields: {required: true, type: Array}})

</script>

<template>
    <tr class="text-center">
        <td>
            <Fa icon="plus-circle"/>
        </td>
        <td>
            <AppBtn icon="plus" variant="success"/>
        </td>
        <td v-for="field in fields" :key="field.name">
            <AppInputGuesser v-if="field.type !== null" :field="field" no-label/>
        </td>
    </tr>
</template>
