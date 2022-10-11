<script setup>
    import {computed} from 'vue'

    const emit = defineEmits(['update:modelValue'])
    const props = defineProps({
        fields: {required: true, type: Array},
        icon: {default: 'search', type: String},
        id: {required: true, type: String},
        label: {default: 'Rechercher', type: String},
        modelValue: {default: () => ({}), type: Object},
        reverseIcon: {default: 'plus', type: String},
        reverseLabel: {default: 'ajout', type: String},
        reverseMode: {default: 'create', type: String},
        send: {required: true, type: Function},
        submit: {required: true, type: Function},
        variant: {default: 'secondary', type: String},
        violations: {default: () => [], type: Array}
    })
    const form = computed(() => `${props.id}-form`)
    const fullReverseLabel = computed(() => `Basculer en mode ${props.reverseLabel}`)
    const lowerLabel = computed(() => props.label.toLowerCase())

    function input(v) {
        emit('update:modelValue', v)
    }

    function reverse() {
        props.send(props.reverseMode)
    }
</script>

<template>
    <tr :id="id">
        <td class="text-center">
            <Fa :icon="icon"/>
            <AppBtn :icon="reverseIcon" :label="fullReverseLabel" @click="reverse"/>
        </td>
        <td class="text-center">
            <AppForm :id="form" class="d-inline m-0 p-0" @submit="submit">
                <AppBtn :icon="icon" :label="label" :variant="variant" type="submit"/>
            </AppForm>
            <slot/>
        </td>
        <AppTableFormField
            v-for="field in fields"
            :key="field.name"
            :field="field"
            :form="form"
            :label="lowerLabel"
            :model-value="modelValue"
            :violations="violations"
            @update:model-value="input"/>
    </tr>
</template>
