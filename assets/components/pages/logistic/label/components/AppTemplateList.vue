<script setup>
    import api from '../../../../../api'
    import {defineProps, onMounted, ref} from 'vue'
    import IconWithText from './IconWithText.vue'

    const props = defineProps({
        labelKind: {required: true, type: String},
        templateFamilly: {required: true, type: String},
        title: {required: true, type: String}
    })
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
        },
        {
            name: 'productDescription',
            label: 'DESIGNATION',
            type: 'text',
            required: true
        },
        {
            name: 'productReference',
            label: 'REF CLIENT',
            type: 'text',
            required: true
        },
        {
            name: 'productIndice',
            label: 'INDICE',
            type: 'text',
            required: true
        }
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
    function updateGeneralTC() {
        console.log('updateGeneralTC', localDataTC.value)
        //Récupération localDataTC et envoie via post pour ajout en base
        localDataTC.value = {...localDataTC.value, labelKind: props.labelKind, templateFamilly: props.templateFamilly}
        const response = api('/api/label-templates','POST', localDataTC.value)
        response.then(data => {
            getLabelTemplatesTC()
        })
    }
    function localDataChangeTC(data) {
        localDataTC.value = data
    }
    const labeltemplatesTC = ref([])
    function getLabelTemplatesTC() {
        api(`/api/label-templates?labelKind=${props.labelKind}&templateFamilly=${props.templateFamilly}`, 'GET')
            .then(response => {
                labeltemplatesTC.value = response['hydra:member']
                console.log(labeltemplatesTC.value)
            })
    }
    function editTC(tcTemplate) {
        console.log('editTC', tcTemplate)
        //localDataTC.value = tcTemplate
    }
    function removeTC(tcTemplate) {
        console.log('removeTC', tcTemplate)
        api(`/api/label-templates/${tcTemplate.id}`, 'DELETE')
            .then(response => {
                getLabelTemplatesTC()
            })
    }
    const showAddForm = ref(false)
    onMounted(() => {
        getLabelTemplatesTC()
    })
    //endregion
</script>

<template>
    <div class="title">{{ title }}</div>
    <AppCardShow
        id="addEtiquetteCartonTC"
        v-if="showAddForm"
        :key="keyTC"
        :fields="cartonTCFields"
        :component-attribute="localDataTC"
        :title="`Ajout modèle d\'étiquette format ${labelKind} => Renseigner les champs ci-dessous`"
        @cancel="cancelTC"
        @update="updateGeneralTC"
        @update:model-value="localDataChangeTC"/>
    <IconWithText
        text="Modèle d'étiquette"
        text-color="yellow"
        label="AJOUT"
        label-color="yellow"
        icon="box"
        icon-color="darkgreen"
        :offset="15"
        :show-edit="false"
        :show-remove="false"
        style="cursor: grab;"
        @click="showAddForm = !showAddForm"
    />
    <div class="col-2" v-for="tcTemplate in labeltemplatesTC" style="cursor: grab;">
        <IconWithText
            :label="tcTemplate.labelName"
            :text="`${tcTemplate.productReference}-${tcTemplate.productIndice}`"
            icon="box"
            icon-color="sandybrown"
            @edit="editTC(tcTemplate)"
            @remove="removeTC(tcTemplate)"
        />
    </div>
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
</style>