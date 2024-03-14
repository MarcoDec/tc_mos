<script setup>
    import AppWorkflow from './AppWorkflow.vue'
    import {computed, ref, watchEffect} from 'vue'
    import api from '../../api'
    const props = defineProps({
        itemIri: {default: null, required: true, type: String},
        workflowToShow: {default: () => [], required: true, type: Array}
    })
    const workflowData = ref([])
    function getItemWorkflowInformationFromAPI() {
        const formData = {
            iri: props.itemIri
        }
        api('/api/workflows/can', 'POST', formData).then(response => {
            workflowData.value = response['hydra:member']
        })
    }
    // Assurez-vous que cette méthode est appelée chaque fois que l'IRI de l'item change.
    watchEffect(() => {
        getItemWorkflowInformationFromAPI()
    })
    const filteredWorkflowData = computed(
        () => workflowData.value.filter(workflow => props.workflowToShow.includes(workflow.workflowName))
    )
    function getDefaultAction(workflow) {
        if (workflow.can.length > 0) {
            return workflow.can[0]
        }
        return ''
    }
    getItemWorkflowInformationFromAPI()
    function applyTransition(data) {
        const formData = {
            iri: props.itemIri,
            transition: data.transition,
            workflowName: data.workflowName
        }
        api('/api/workflows/apply', 'POST', formData).then(response => {
            console.log(response)
            window.location.reload()
        })
    }
</script>

<template>
    <div class="d-flex flex-row">
        <div v-for="workflow in filteredWorkflowData" :key="`wf_${workflow.workflowName}`" class="wf-item">
            <AppWorkflow
                :possible-actions="workflow.can"
                :current-state="workflow.currentState"
                :default-action="getDefaultAction(workflow)"
                :workflow-name="workflow.workflowName"
                @apply-transition="applyTransition"/>
        </div>
    </div>
</template>

<style scoped>
    .wf-item {
        margin-left: 10px;
        max-width: 300px;
    }
</style>
