<script setup>
    import {computed} from 'vue'
    import {get} from 'lodash'

    const props = defineProps({
        field: {required: true, type: Object},
        item: {required: true, type: Object},
        row: {required: true, type: String}
    })
    const bool = computed(() => props.field.type === 'boolean')
    const color = computed(() => props.field.type === 'color')
    const id = computed(() => `${props.row}-${props.field.name}`)
    const value = computed(() => get(props.item, props.field.name))
    const label = computed(() => {
        if (typeof props.field.labelValue === 'function') {
            return props.field.labelValue(value.value)
        }
        return value.value
    })
    const input = computed(() => `${id.value}-input`)
    const array = computed(() => Array.isArray(label.value))
</script>

<template>
    <td :id="id">
        <AppInputGuesser v-if="bool" :id="input" :field="field" :model-value="label" disabled form="none"/>
        <div v-else-if="color" class="row">
            <div v-if="!field.hideLabelValue" class="col-2">
                {{ label }}
            </div>
            <div class="col">
                <AppInputGuesser :id="input" :field="field" :model-value="label" disabled form="none"/>
            </div>
        </div>
        <ul v-else-if="array">
            <li v-for="(v, i) in label" :key="i">
                {{ v }}
            </li>
        </ul>
        <template v-else>
            {{ label }}
        </template>
    </td>
</template>
