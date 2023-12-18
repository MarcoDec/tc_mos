<script setup>
    import {computed, onMounted, ref} from 'vue'
    import {useRoute, useRouter} from 'vue-router'
    import api from '../../../../api'
    import AppItemCarte from './components/AppItemCarte.vue'
    import AppStepOperateur from './components/AppStepOperateur.vue'
    import AppStepOrderFabrication from './components/AppStepOrderFabrication.vue'
    import AppStepProduit from './components/AppStepProduit.vue'
    import AppStepProgress from './components/AppStepProgress.vue'
    import CustomeSelect from './components/CustomeSelect.vue'

    const route = useRoute()
    const router = useRouter()
    const idLabelTemplate = route.params.idLabelTemplate
    const response = api(`/api/label-templates/${idLabelTemplate}`, 'get')
    const modeleEtiquette = ref({})

    response.then(data => {
        modeleEtiquette.value = data
    })
    const operateur = ref({ name: '<à définir>'})
    const currentStep = ref(1)
    const zpl = ref('')
    const zplHref = ref('')
    const imageUrl = ref('')
    const newLabel = ref({})

    function getOperateur(operateurData) {
        console.log('operateurData', operateurData)
        operateur.value = operateurData
        currentStep.value += 1
    }
    const of = ref({})
    function getOf(ofData) {
        console.log('ofData', ofData)
        of.value = ofData
        currentStep.value += 1
    }
    const products = ref({})
    function getProducts(productsData) {
        console.log('productsData', productsData)
        products.value= productsData
        currentStep.value += 1
    }
    const nbProduit = ref(0)

    const steps = ref([
        {id: 1, label: 'Opérateur'},
        {id: 2, label: 'OF'},
        {id: 3, label: 'Scan Produits'},
        {id: 4, label: 'Impression', icon: 'print'}
    ])
    function changeTemplate() {
        router.push({name: 'label-template-list'})
    }
    function resetAll() {
        currentStep.value = 1
    }
    function restartFromOf() {
        currentStep.value = 2
    }
    function restartNewCarton() {
        currentStep.value = 3
    }
    function disconnect() {
        // route.push({name: 'AppLabelTemplateList'})
    }
    const printers = ref([])
    function getPrinters() {
        api('/api/printers', 'get')
            .then(data => {
                printers.value = data['hydra:member']
            })
    }
    getPrinters()
    const selectedPrinter = ref(null)
    function onNetworkPrinterSelected(printer) {
        selectedPrinter.value = printer
    }
    function imprimeLocal() {
        window.print()
    }
    function imprimeReseau() {
        if (selectedPrinter.value === null) {
            alert('Veuillez choisir une imprimante réseau')
            return
        }
        const data = {
            printer: selectedPrinter.value['@id']
        }
        api(`/api/label-cartons/${newLabel.value.id}/print`, 'post', data)
            .then(() => {
                alert('Impression lancée')
            })
    }
    const ofNumberAndIndice = computed(() => {
        if (of.value === '<à définir>' || !of.value.data) return '<à définir>'
        return `${of.value.data.ofnumber}.${of.value.data.indice}`
    })
    const productRefAndIndice = computed(() => {
        if (of.value === '<à définir>' || !of.value.data) return '<à définir>'
        return `${of.value.data.productRef}/${of.value.data.productIndice}`
    })
</script>

<template>
    <div class="carton-label container-fluid">
        <div class="row">
            <div class="col-6">
                <ul>
                    <AppItemCarte
                        label="Famille :">
                        <Fa
                            v-if="modeleEtiquette.templateFamily === 'carton'"
                            :brand="false"
                            class="color-carton font-size-15px"
                            icon="box-open"/>
                    </AppItemCarte>
                    <AppItemCarte label="Format :" :value="modeleEtiquette.labelKind"/>
                    <AppItemCarte label="Modèle :" :value="modeleEtiquette.labelName"/>
                </ul>
            </div>
            <div class="col-6">
                <ul>
                    <AppItemCarte label="Opérateur :" :value="operateur.name"/>
                    <AppItemCarte label="OF :" :value="ofNumberAndIndice"/>
                    <AppItemCarte label="Produit :" :value="productRefAndIndice"/>
                </ul>
            </div>
            <div class="col-12">
                <AppItemCarte label="Nb Produit scannés :">
                    <strong style="font-size: 50px;">{{ nbProduit }}</strong>
                </AppItemCarte>
            </div>
        </div>
    </div>
    <AppStepProgress :current-step="currentStep" :steps="steps"/>
    <div class="step-forms">
        <AppStepOperateur
            v-if="currentStep === 1"
            class="form-step"
            @next-step="getOperateur"
        />
        <AppStepOrderFabrication
            v-if="currentStep === 2"
            class="form-step"
            @next-step="getOf"
        />
        <AppStepProduit
            v-if="currentStep === 3"
            class="form-step"
            :modele-etiquette="modeleEtiquette"
            :of="of"
            :operateur="operateur"
            @change-products="nbProduit = $event"
            @next-step="getProducts"/>
        <div v-show="currentStep === 4" class="form-step">
            <div class="step-title">Impression</div>
            <img id="imageToPrint" class="toPrint" :src="imageUrl" alt="aperçu etiquette" width="280"/>
            <div class="d-flex flex-row align-items-stretch align-self-stretch justify-content-center">
                <a :href="zplHref" download="etiquette.zpl" class="btn btn-info d-flex justify-content-center min-button">
                    <Fa :brand="false" icon="download"/> ZPL
                </a>
            </div>
            <div class="d-flex flex-row align-items-stretch align-self-stretch justify-content-center mt-4">
                <CustomeSelect
                    :options="printers"
                    title="Choix Imprimantes réseau"
                    @update:model-value="onNetworkPrinterSelected"/>
                <button class="btn btn-primary d-inline-block d-flex flex-column justify-content-center min-button align-items-center" @click="imprimeReseau">
                    <Fa :brand="false" icon="network-wired"/> Réseau
                </button>
            </div>

            <div class="align-items-stretch align-self-stretch d-flex flex-row justify-content-center mt-4">
                <button class="align-items-center btn btn-success d-flex d-inline-block flex-column justify-content-center min-button" @click="imprimeLocal">
                    <Fa :brand="false" icon="print"/> Local
                </button>
            </div>
        </div>
        <div v-show="currentStep >= 5" class="form-step">
            <div class="step-title">
                Choix
            </div>
            <div class="align-items-center d-flex flex-column">
                <button class="btn btn-warning d-inline-block m-2" @click="changeTemplate">
                    Changer de modèle
                </button>
                <button class="btn btn-warning d-inline-block m-2" @click="resetAll">
                    Recommencer depuis le début
                </button>
                <button class="btn btn-success d-inline-block m-2" @click="restartFromOf">
                    changer d'OF
                </button>
                <button class="btn btn-success d-inline-block m-2" @click="restartNewCarton">
                    Faire un autre carton
                </button>
                <button class="btn btn-success d-inline-block m-2" @click="disconnect">
                    Quitter
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
    .value-list li {
        padding: 5px;
        cursor: pointer;
    }

    .value-list li::after {
        content: '';
        display: inline-block;
        width: 10px;
        height: 10px;
        margin-left: 5px;
        /* La couleur sera définie dynamiquement */
    }
    .step-forms {
        display: flex;
        justify-content: center;
        margin: 0;
    }
    .step-forms .form-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: space-between;
        width: 300px;
        min-height: 20px;
        border: 1px solid black;
        border-radius: 10px;
    }
    .carton-label {
        font-family: 'Arial', sans-serif;
        font-size: 10px;
        max-width: 350px;
        margin: 0px auto;
        padding: 5px;
        background-color: #f8eec9;
        border: 1px solid #000000;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .carton-label ul {
        list-style-type: none;
        padding: 0;
        margin: 0;
    }

    .carton-label li {
        display: flex;
        justify-content: space-between;
        border-bottom: 1px solid #eee;
    }

    .carton-label li:last-child {
        border-bottom: none;
    }

    .carton-label li::before {
        content: attr(data-label);
        font-weight: bold;
        margin-right: 10px;
    }
    .color-carton {
        color: #A4683BFF;
    }
    .font-size-15px {
        font-size: 15px;
    }
    .step-title {
        width: 100%;
        text-align: center;
        font-size: 10px;
        font-weight: bold;
        background-color: #6c757d;
        color: white;
    }
    .min-button {
        min-height: 65px;
        min-width: 65px;
    }
    .inputOfProduct {
        width: 180px;
    }
    .labelOfProduct {
        min-width: 60px;
    }
</style>
