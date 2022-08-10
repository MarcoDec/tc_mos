<script setup>
    import {computed, defineProps} from 'vue'

    const props = defineProps({field: {required: true, type: Array}})
    const alignfields = computed(() => props.fields
        .map(field => {
            function nulField(index){
                return {name: `${field.name}-${index}`, type: null}
            }
            if (Array.isArray(field.children) && field.children.length > 0){
                return [nulField(1), nulField(2), ...field.children]
            }
            return field
        })
        .flat())
</script>

<template>
    <tr class="text-center">
        <td>
            <Fa icon="plus-circle"/>
        </td>
        <td>
            <AppBtn icon="plus" variant="success"/>
        </td>
        <td v-for="field in alignfields" :key="field.name">
            <AppInputGuesser v-if="field.type !== null" :field="field" no-label/>
        </td>
    </tr>
</template>
