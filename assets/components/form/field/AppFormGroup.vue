<script setup>
    import AppInputGuesser from './input/AppInputGuesser'
    import AppLabel from './AppLabel.vue'
    import {computed} from 'vue'

    const props = defineProps({field: {required: true, type: Object}, form: {required: true, type: String}})
    const input = computed(() => `${props.form}-${props.field.name}`)
</script>

<template>
    <div class="mb-3 row">
        <AppLabel :field="field" :input="input"/>
        <AppInputGuesser :id="input" :field="field" :form="form" class="col"/>
    </div>
</template>
