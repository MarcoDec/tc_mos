<script setup>
    import {inject} from 'vue'

    const emit = defineEmits(['toggle'])
    const fields = inject('fields', [])

    function toggle() {
        emit('toggle')
    }
</script>

<template>
    <tr class="text-center">
        <td>
            <Fa icon="plus-circle"/>
        </td>
        <td>
            <AppBtn icon="filter" variant="secondary" @click="toggle"/>
            <AppBtn icon="plus" variant="success"/>
        </td>
        <td v-for="field in fields" :key="field.name">
            <AppInputGuesser v-if="field.create" :field="field" no-label/>
        </td>
    </tr>
</template>
