<script setup>
    import { defineProps} from 'vue'
    const props = defineProps({
        fields: { required: true, type: Array },
    })
</script>

<template>
    <tr class="text-center">
        <td>
            <AppBtn icon="plus" variant="success"/>
        </td>
        <td v-for="field in fields" :key="field.name">
            <AppInputGuesser v-if="(field.type !== null) && (field.name !== 'prices') " :field="field" no-label/>
        </td>
    </tr>
</template>
