<script setup>
    import {computed} from 'vue'
    import {useRoute} from 'vue-router'

    defineProps({
        fields: {required: true, type: Object},
        machine: {required: true, type: Object},
        modelValue: {default: () => ({}), type: Object},
        submitLabel: {default: 'Modifier', type: String}
    })

    const emit = defineEmits(['submit'])
    const route = useRoute()
    const id = computed(() => `${route.name}-form`)

    function submit(data) {
        emit('submit', data)
    }
</script>

<template>
    <AppFormGenerator
        :id="id"
        :fields="fields"
        :model-value="modelValue"
        :submit-label="submitLabel"
        :violations="machine.state.value.context.violations"
        @submit="submit"/>
</template>
