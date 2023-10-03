<script setup>
    import {onMounted, ref} from 'vue'
    import AppSuspense from '../../AppSuspense.vue'
    import api from '../../../api'
    import AppFormCardable from '../AppFormCardable'

    const props = defineProps({
        fields: {required: true, type: Array},
        form: {required: true, type: String},
        id: {required: true, type: String},
        modelValue: {default: () => ({}), type: Object},
        cardTitle: {default: 'Ajout', type: String},
        btnLabel: {default: 'Enregistrer', type: String},
        apiUrl: {required: true, type: String},
        apiMethod: {required: true, type: String}
    })
    const emits = defineEmits(['cancel', 'error', 'submitted', 'update:model-value'])
    const isPopupVisible = ref(false)
    const localData = ref({})
    let violations = []
    onMounted(() => {
        localData.value = props.modelValue
    })
    function annule(){
        isPopupVisible.value = false
        emits('cancel')
    }
    function normalizeLocalData() {
        const normalizedLocalData = localData.value
        props.fields.forEach(field => {
            console.log(`normalize ${field}`, field)
        })
        return normalizedLocalData
    }

    async function onSubmit() {
        if (props.apiMethod === 'POST') {
            try {
                const result = await api(props.apiUrl, props.apiMethod, normalizeLocalData)
                emits('submitted', result)
            } catch (e) {
                violations = e
                emits('error', e)
            }
        }
        if (props.apiMethod === 'PATCH') {
            try {
                const result = await api(`${localData.value['@id']}`, props.apiMethod, normalizeLocalData)
                emits('submitted', result)
            } catch (e) {
                violations = e
                emits('error', e)
            }
        }
        isPopupVisible.value = violations.length > 0
    }
    function onUpdateModelValue(data) {
        localData.value = {...data}
        emits('update:model-value', data)
    }
</script>

<template>
    <AppSuspense>
        <AppCard :id="id" class="bg-blue col" title="">
            <AppRow>
                <button id="btnRetour1" class="btn btn-danger btn-icon btn-sm col-1" @click="annule">
                    <Fa icon="angle-double-left"/>
                </button>
                <h4 class="col">
                    <Fa icon="plus"/> {{ cardTitle }}
                </h4>
            </AppRow>
            <br/>
            <AppFormCardable :id="form" :fields="fields" :model-value="localData" label-cols @update:model-value="onUpdateModelValue"/>
            <div v-if="isPopupVisible" class="alert alert-danger" role="alert">
                <div v-for="violation in violations" :key="violation">
                    <li>{{ violation.message }}</li>
                </div>
            </div>
            <AppCol class="btnright">
                <AppBtn class="btn-float-right" label="Ajout" variant="success" size="sm" @click="onSubmit">
                    <Fa icon="plus"/> {{ btnLabel }}
                </AppBtn>
            </AppCol>
        </AppCard>
    </AppSuspense>
</template>

