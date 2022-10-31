<script setup>
    import {reactive, ref} from 'vue'
    import AppFormGroup from './field/AppFormGroup.vue'

    const emit = defineEmits(['submit'])
    const form = ref()
    const props = defineProps({
        disabled: {type: Boolean},
        fields: {required: true, type: Object},
        id: {required: true, type: String},
        submitLabel: {default: 'Modifier', type: String},
        violations: {default: () => [], type: Array}
    })
    const value = reactive({})

    function input(field, v) {
        value[field.name] = v
    }

    function submit() {
        let normalizedValue = null
        if (props.fields.file)
            normalizedValue = new FormData(form.value.$el)
        else {
            normalizedValue = {}
            for (const field of props.fields.fields)
                normalizedValue[field.name] = value[field.name]
        }
        emit('submit', normalizedValue)
    }
</script>

<template>
    <AppForm :id="id" ref="form" @submit="submit">
        <AppFormGroup
            v-for="field in fields.fields"
            :key="field.name"
            :disabled="disabled"
            :field="field"
            :form="id"
            :model-value="value"
            :violations="violations"
            @input="input"/>
        <div class="row">
            <div class="col d-inline-flex justify-content-end">
                <AppBtn :disabled="disabled" :label="submitLabel" type="submit"/>
            </div>
        </div>
    </AppForm>
</template>
