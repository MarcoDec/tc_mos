<script setup>
    import AppWorkflow from './AppWorkflow.vue'
    import {computed, ref} from 'vue'
    import api from "../../api";
    const props = defineProps({
        itemIri: {default: null, required: true, type: String},
        workflowToShow: {default:()=>[], required: true, type: Array}
    })
    const workflowData = ref([])
    function getItemWorkflowInformationFromAPI() {
        //appelle l'api '/api/workflows/can' et passe dans le body l'iri de l'item
        const formData ={
            iri: props.itemIri
        }
        api('/api/workflows/can', 'POST', formData).then(response => workflowData.value = response['hydra:member'])
    }
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

</script>

<template>
    <div class="d-flex flex-row">
        <div v-for="workflow in filteredWorkflowData" :key="`wf_${workflow.workflowName}`" style="margin-left: 10px; max-width: 300px">
            <AppWorkflow :possible-actions="workflow.can" :current-state="workflow.currentState" :default-action="getDefaultAction(workflow)" :workflow-name="workflow.workflowName"/>
        </div>
    </div>
</template>

<style scoped>
</style>