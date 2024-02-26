<script setup>
    import api from '../../../../../api'
    import {defineProps, onMounted, ref} from 'vue'
    import IconWithText from './IconWithText.vue'
    import {useRouter} from 'vue-router'

    const props = defineProps({
        labelKind: {required: true, type: String},
        templateFamilly: {required: true, type: String},
        title: {required: true, type: String}
    })
    const router = useRouter()
    //region définition des éléments pour gestion cartons format TConcept
    const cartonTCFields = [
        {
            name: 'labelName',
            label: 'Désignation Etiquette Carton',
            type: 'text',
            required: true
        },
        {
            name: 'customerAddressName',
            label: 'DESTINATAIRE',
            type: 'text',
            required: true
        },
        {
            name: 'manufacturer',
            label: 'EXPEDITEUR',
            type: 'text',
            required: true
        }
        //,
        // {
        //     name: 'productDescription',
        //     label: 'DESIGNATION',
        //     type: 'text',
        //     required: true
        // },
        // {
        //     name: 'productReference',
        //     label: 'REF CLIENT',
        //     type: 'text',
        //     required: true
        // },
        // {
        //     name: 'productIndice',
        //     label: 'INDICE',
        //     type: 'text',
        //     required: true
        // }
    ]
    const keyTC = ref(0)
    const localDataTC = ref({})
    function cancelTC() {
        console.log('cancelTC')
        localDataTC.value = {
            labelName: '',
            customerAddressName: '',
            manufacturer: '',
            productDescription: '',
            productReference: '',
            productIndice: ''
        }
        keyTC.value++
    }
    function getLabelTemplatesTC() {
        api(`/api/label-templates?labelKind=${props.labelKind}&templateFamilly=${props.templateFamilly}`, 'GET')
            .then(response => {
                labeltemplatesTC.value = response['hydra:member']
                console.log(labeltemplatesTC.value)
            })
    }
    function updateGeneralTC() {
        console.log('updateGeneralTC', localDataTC.value)
        //Récupération localDataTC et envoie via post pour ajout en base
        let width = 0 //inch
        let height = 0 //inch
        switch (props.labelKind) {
            case 'TConcept':
                width = 4
                height = 6
                break
            case 'ETI9':
                width = 3
                height = 8.5
                break
        }
        localDataTC.value = {
            ...localDataTC.value,
            labelKind: props.labelKind,
            templateFamilly: props.templateFamilly,
            width,
            height
        }
        const response = api('/api/label-templates', 'POST', localDataTC.value)
        response.then(() => {
            getLabelTemplatesTC()
        })
    }
    function localDataChangeTC(data) {
        localDataTC.value = data
    }
    const labeltemplatesTC = ref([])
    function editTC(tcTemplate) {
        console.log('editTC', tcTemplate)
        //localDataTC.value = tcTemplate
    }
    function generateLabels(tcTemplate) {
        console.log('generateLabels', tcTemplate)
        router.push({name: 'label-template-generate', params: {idLabelTemplate: tcTemplate.id}})
    }
    function removeTC(tcTemplate) {
        const validate = confirm(`Voulez-vous vraiment supprimer le modèle d'étiquette ${tcTemplate.labelName}?`)
        if (!validate) return
        api(`/api/label-templates/${tcTemplate.id}`, 'DELETE')
            .then(() => {
                getLabelTemplatesTC()
            })
    }
    const showAddForm = ref(false)
    function onShowAddForm() {
        if (showAddForm.value) cancelTC()
        showAddForm.value = !showAddForm.value
        //console.log('onShowAddForm', showAddForm.value)
        keyTC.value++
    }
    onMounted(() => {
        getLabelTemplatesTC()
    })
    //endregion
</script>

<template>
    <div class="title">
        {{ title }}
    </div>
    <AppCardShow
        v-if="showAddForm"
        id="addEtiquetteCartonTC"
        :key="keyTC"
        class="mb-3"
        :fields="cartonTCFields"
        :component-attribute="localDataTC"
        :title="`Ajout modèle d\'étiquette format ${labelKind} => Renseigner les champs ci-dessous`"
        @cancel="cancelTC"
        @update="updateGeneralTC"
        @update:model-value="localDataChangeTC"/>
    <IconWithText
        class="icon-with-text"
        text="Modèle d'étiquette"
        text-color="yellow"
        label="AJOUT"
        label-color="yellow"
        icon="box"
        icon-color="darkgreen"
        :offset="15"
        :show-edit="false"
        :show-remove="false"
        @click="onShowAddForm"/>
    <IconWithText
        v-for="(tcTemplate, index) in labeltemplatesTC"
        :key="index"
        class="icon-with-text"
        :label="tcTemplate.labelName"
        :text="`${tcTemplate.manufacturer}`"
        icon="box"
        icon-color="#A4683BFF"
        :offset="15"
        @click="generateLabels(tcTemplate)"
        @edit="editTC(tcTemplate)"
        @remove="removeTC(tcTemplate)"/>
</template>

<style scoped>
    .title {
        font-size: 20px;
        font-weight: bold;
        margin-bottom: 10px;
        text-align: center;
        background-color: #6c757d;
        color: white;
        border: 1px solid white;
    }
    .icon-with-text {
        cursor: grab;
    }
</style>
