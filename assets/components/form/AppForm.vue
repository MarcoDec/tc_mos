<script setup>
    import {computed, reactive} from 'vue'
    import AppBtn from '../AppBtn.vue'
    import AppFormGroup from './field/AppFormGroup.vue'

    const emit = defineEmits(['submit'])
    const props = defineProps({
        fields: {required: true, type: Array},
        id: {required: true, type: String},
        submitLabel: {required: true, type: String}
    })
    const value = reactive({})
    const normalizedValue = computed(() => {
        const v = {}
        for (const field of props.fields)
            v[field.name] = value[field.name]
        return v
    })

    function input(field, v) {
        value[field.name] = v
    }

    function submit(e) {
        e.preventDefault()
        emit('submit', normalizedValue.value)
    }
</script>

<template>
    <form :id="id" autocomplete="off" enctype="multipart/form-data" method="post" novalidate @submit="submit">
        <AppFormGroup v-for="field in fields" :key="field.name" :field="field" :form="id" @input="input"/>
        <div class="row">
            <div class="col d-inline-flex justify-content-end">
                <AppBtn :label="submitLabel" type="submit"/>
            </div>
        </div>
    </form>
</template>
