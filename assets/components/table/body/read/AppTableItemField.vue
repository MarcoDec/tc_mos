<script setup>
    import {computed} from 'vue'

    const props = defineProps({
        field: {required: true, type: Object},
        item: {required: true, type: Object},
        row: {required: true, type: String}
    })
    const bool = computed(() => props.field.type === 'boolean')
    const id = computed(() => `${props.row}-${props.field.name}`)
    const value = computed(() => props.item[props.field.name])
    const label = computed(() => (props.field.type === 'select' ? props.field.options.label(value.value) : value.value))
    const input = computed(() => `${id.value}-input`)
    const isArray = computed(() => Array.isArray(label.value))
</script>

<template>
    <td :id="id">
        <AppInputGuesser v-if="bool" :id="input" :field="field" :model-value="label" disabled form="none"/>
        <ul v-else-if="isArray">
            <li v-for="(v, i) in label" :key="i">
                {{ v }}
            </li>
        </ul>
        <template v-else>
            {{ label }}
        </template>
    </td>
</template>
