<script setup>
    import {computed} from 'vue'

    const emit = defineEmits(['update:modelValue'])
    const props = defineProps({
        canReverse: {type: Boolean},
        disableAdd: {required: false, type: Boolean},
        fields: {required: true, type: Object},
        icon: {default: 'search', type: String},
        id: {required: true, type: String},
        label: {default: 'Rechercher', type: String},
        mode: {required: true, type: String},
        modelValue: {default: () => ({}), type: Object},
        reverseIcon: {default: 'plus', type: String},
        reverseLabel: {default: 'ajout', type: String},
        reverseMode: {default: 'create', type: String},
        send: {required: true, type: Function},
        store: {required: true, type: Object},
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
            <template v-if="canReverse">
                <Fa :icon="icon"/>
                <AppBtn v-if="!disableAdd" :icon="reverseIcon" :label="fullReverseLabel" @click="reverse"/>
            </template>
        </td>
        <td class="text-center">
            <slot
                :fields="fields"
                :form="form"
                :icon="icon"
                :label="label"
                :send="send"
                :store="store"
                :submit="submit"
                :variant="variant"
                name="form">
                <AppForm :id="form" class="d-inline m-0 p-0" @submit="submit">
                    <AppBtn :icon="icon" :label="label" :variant="variant" type="submit"/>
                </AppForm>
            </slot>
            <slot/>
        </td>
        <AppTableFormField
            v-for="field in fields.fields"
            :key="field.name"
            :field="field"
            :form="form"
            :label="lowerLabel"
            :mode="mode"
            :model-value="modelValue"
            :store="store"
            :violations="violations"
            @update:model-value="input">
            <template #default="args">
                <slot :name="field.name" v-bind="args"/>
            </template>
        </AppTableFormField>
    </tr>
</template>
