<script setup>
    import {useRoute} from 'vue-router'
    const route = useRoute()
    const idLabelTemplate = route.params.idLabelTemplate
    console.log('idLabelTemplate', idLabelTemplate)
</script>

<template>
    HELLO WORLD {{ idLabelTemplate }}
</template>

<style scoped>
    .currentStep {
        background-color: #A4683BFF;
        color: white;
    }
    .nextStep {
        background-color: #A4683BFF;
        color: white;
    }
    .previousStep {
        background-color: #A4683BFF;
        color: white;
    }
</style>
