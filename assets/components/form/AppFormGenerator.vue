<script setup>
    import {computed, reactive} from 'vue'
    import AppFormGroup from './field/AppFormGroup.vue'

    const emit = defineEmits(['submit'])
    const props = defineProps({
        disabled: {type: Boolean},
        fields: {required: true, type: Object},
        id: {required: true, type: String},
        submitLabel: {default: 'Modifier', type: String}
    })
    const value = reactive({})
    const normalizedValue = computed(() => {
        const v = {}
        for (const field of props.fields.fields)
            v[field.name] = value[field.name]
        return v
    })

    function input(field, v) {
        value[field.name] = v
    }

    function submit() {
        emit('submit', normalizedValue.value)
    }
</script>

<template>
    <AppForm :id="id" @submit="submit">
        <AppFormGroup
            v-for="field in fields.fields"
            :key="field.name"
            :disabled="disabled"
            :field="field"
            :form="id"
            @input="input"/>
        <div class="row">
            <div class="col d-inline-flex justify-content-end">
                <AppBtn :disabled="disabled" :label="submitLabel" type="submit"/>
            </div>
        </div>
    </AppForm>
</template>
