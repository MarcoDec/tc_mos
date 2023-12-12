<script setup>
    import AppTab from '../../../tab/AppTab.vue'
    import AppTabs from '../../../tab/AppTabs.vue'
    import {ref} from 'vue'

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
    const cartonETI9Fields = [
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
    const keyETI = ref(0)
    const localDataTC = ref({})
    const localDataETI9 = ref({})
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
    function cancelETI9() {
        console.log('cancelETI9')
        localDataETI9.value = {
            labelName: '',
            customerAddressName: '',
            manufacturer: '',
            productDescription: '',
            productReference: '',
            productIndice: ''
        }
        keyETI.value++
    }
    function updateGeneralTC() {
        console.log('updateGeneralTC')
        //Récupération localData et envoie via post pour ajout en base
    }
    function updateGeneralETI9() {
        console.log('updateGeneralETI9')
        //Récupération localData et envoie via post pour ajout en base
    }
    function localDataChangeTC(data) {
        console.log('localDataChangeTC', data)
    }
    function localDataChangeETI9(data) {
        console.log('localDataChangeETI9', data)
    }
</script>

<template>
    <AppTabs id="label-templates">
        <!--        <AppTab id="component-labels" title="Modèles Etiquettes Composant" tabs="label-templates" icon="">-->
        <!--            Hello World-->
        <!--        </AppTab>-->
        <!--        <AppTab id="product-labels" title="Modèles Etiquettes Produit" tabs="label-templates" icon="">-->
        <!--            Hello World-->
        <!--        </AppTab>-->
        <AppTab id="carton-labels" title="Modèles Etiquettes Carton" tabs="label-templates" icon="box-open">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-6">
                        <AppCardShow
                            id="addEtiquetteCartonTC"
                            :key="keyTC"
                            :fields="cartonTCFields"
                            :component-attribute="localDataTC"
                            title="Génération de modèle d'étiquette format TCONCEPT - Saisie des champs invariants"
                            @cancel="cancelTC"
                            @update="updateGeneralTC"
                            @update:model-value="localDataChangeTC"/>
                    </div>
                    <div class="col-6">
                        <AppCardShow
                            id="addEtiquetteCartonETI9"
                            :key="keyETI"
                            :fields="cartonETI9Fields"
                            :component-attribute="localDataETI9"
                            title="Génération de modèle d'étiquette format ETI9 - Saisie des champs invariants"
                            @cancel="cancelETI9"
                            @update="updateGeneralETI9"
                            @update:model-value="localDataChangeETI9"/>
                    </div>
                </div>
            </div>
        </AppTab>
        <!--        <AppTab id="pallet-labels" title="Modèles Etiquettes Palette" tabs="label-templates" icon="">-->
        <!--            Hello World-->
        <!--        </AppTab>-->
    </AppTabs>
</template>

<style scoped>
    div.active { position: relative; z-index: 0; overflow: scroll; max-height: 100%}
</style>
