<script setup>
    import {useCookies} from '@vueuse/integrations/useCookies'
    import {computed, onMounted, ref} from 'vue'
    import {useRoute, useRouter} from 'vue-router'
    import useUser from '../../../../stores/security'
    import api from '../../../../api'
    import AppSwitch from '../../../form-cardable/fieldCardable/input/AppSwitch.vue'
    import AppItemCarte from './components/AppItemCarte.vue'
    import AppStepBase from './components/AppStepBase.vue'
    import AppStepOperateur from './components/AppStepOperateur.vue'
    import AppStepOrderFabrication from './components/AppStepOrderFabrication.vue'
    import AppStepPrint from './components/AppStepPrint.vue'
    import AppStepProduit from './components/AppStepProduit.vue'
    import AppStepProgress from './components/AppStepProgress.vue'
    import CustomeSelect from './components/CustomeSelect.vue'

    const route = useRoute()
    const router = useRouter()
    const idLabelTemplate = route.params.idLabelTemplate
    const response = api(`/api/label-templates/${idLabelTemplate}`, 'get')
    const modeleEtiquette = ref({})
    const initPosteError = ref(null)
    const isPosteConfigurationChangeNeed = ref(false)
    const cookies = useCookies(['token'])
    const operateur = ref({name: '<à définir>'})
    const currentStep = ref(0)
    const of = ref({})
    const products = ref({})
    const nbProduit = ref(0)
    const manufacturer = ref('')
    const steps = ref([
        {id: 0, label: 'Choix GP/Antenne'},
        {id: 1, label: 'Opérateur'},
        {id: 2, label: 'OF'},
        {id: 3, label: 'Scan Produits'},
        {id: 4, label: 'Impression', icon: 'print'}
    ])
    const ofNumberAndIndice = computed(() => {
        if (of.value === '<à définir>' || !of.value.data) return '<à définir>'
        return `${of.value.data.ofnumber}.${of.value.data.indice}`
    })
    const productRefAndIndice = computed(() => {
        if (of.value === '<à définir>' || !of.value.data) return '<à définir>'
        return `${of.value.data.productRef}/${of.value.data.productIndice}`
    })
    const currentUser = useUser()
    const selectedPrinter = ref(null)
    const printers = ref([])
    const selectedPrinterName = computed(() => printers.value.find(printer => printer['@id'] === selectedPrinter.value).name)
    const temporaryPrinter = ref(null)
    const temporaryPosteName = ref(null)
    const currentId = ref(null)
    const singlePrinterMobileUnitDefined = ref(false)
    const localPrint = ref(false)
    const printerMobileUnitName = ref(null)
    const originGP = ref(true)

    response.then(data => {
        modeleEtiquette.value = data
    })

    function getOperateur(operateurData) {
        operateur.value = operateurData
        currentStep.value += 1
    }
    function getOf(ofData) {
        of.value = ofData
        manufacturer.value = ofData.data.customer.id_soc_gest_customer === 2 ? 'MG2C' : 'TCONCEPT'
        currentStep.value += 1
    }
    function getProducts(productsData) {
        products.value = productsData
        currentStep.value += 1
    }
    function changeTemplate() {
        router.push({name: 'label-template-list'})
    }
    function resetAll() {
        window.location.reload()
    }
    function restartFromOf() {
        nbProduit.value = 0
        of.value = {}
        currentStep.value = 2
    }
    function restartNewCarton() {
        nbProduit.value = 0
        currentStep.value = 3
    }
    function disconnect() {
        router.push({name: 'login'})
    }

    function onPrinted() {
        currentStep.value += 1
    }

    function getPrinters() {
        api('/api/printers', 'get')
            .then(data => {
                printers.value = data['hydra:member']
            })
    }
    getPrinters()
    function onNetworkPrinterSelected(printer) {
        temporaryPrinter.value = printer
    }
    function updatePoste() {
        if (!temporaryPosteName.value) {
            initPosteError.value = 'Le nom du poste est obligatoire'
            return
        }
        let networkPrinter = null
        if (temporaryPrinter.value && temporaryPrinter.value['@id'] && !localPrint.value) {
            networkPrinter = temporaryPrinter.value['@id']
            initPosteError.value = null
        }
        if (!localPrint.value && (!temporaryPrinter.value || !temporaryPrinter.value['@id'])) {
            initPosteError.value = 'Le choix d\'une imprimante réseau est obligatoire'
            return
        }
        const data = {
            printer: networkPrinter,
            name: temporaryPosteName.value,
            localPrint: localPrint.value
        }
        fetch(`/api/single-printer-mobile-units/${currentId.value}`, {
            method: 'PATCH',
            headers: {
                'Content-Type': 'application/merge-patch+json',
                Authorization: `Bearer ${cookies.get('token')}`
            },
            body: JSON.stringify(data)
        })
            .then(response2 => {
                if (!response2.ok) {
                    throw new Error(`Network response was not ok ${response2.statusText}`)
                }
                return response2.json() // ou response.text() si la réponse n'est pas au format JSON
            })
            .then(() => {
                window.location.reload()
            })
    }
    function addNewPoste() {
        if (!temporaryPosteName.value) {
            initPosteError.value = 'Le nom du poste est obligatoire'
            return
        }
        let networkPrinter = null
        if (temporaryPrinter.value && temporaryPrinter.value['@id'] && !localPrint.value) {
            networkPrinter = temporaryPrinter.value['@id']
            initPosteError.value = null
        }
        if (!localPrint.value && (!temporaryPrinter.value || !temporaryPrinter.value['@id'])) {
            initPosteError.value = 'Le choix d\'une imprimante réseau est obligatoire'
            return
        }
        const data = {
            printer: networkPrinter,
            name: temporaryPosteName.value,
            localPrint: localPrint.value
        }
        api('/api/single-printer-mobile-units/addNewAndLink', 'post', data)
            .then(() => {
                window.location.reload()
            })
    }
    function lierImprimante() {
        if (isPosteConfigurationChangeNeed.value) {
            updatePoste()
        } else {
            addNewPoste()
        }
    }
    function updateLocalPrint(value) {
        localPrint.value = value
    }
    function onPosteConfigurationChange() {
        isPosteConfigurationChangeNeed.value = true
        temporaryPosteName.value = printerMobileUnitName.value
        temporaryPrinter.value = printers.value.find(printer => printer['@id'] === selectedPrinter.value)
        singlePrinterMobileUnitDefined.value = false
    }
    function getBase(data) {
        originGP.value = data.originGP
        currentStep.value += 1
    }
    onMounted(async () => {
        if (!currentUser.isLogged) router.push({name: 'login'})
        //if (!currentUser.isLogisticsWriter || !currentUser.isProductionWriter) router.push({name: 'home'})
        //Récupération de l'imprimante associée au poste
        // via l'api /api/single-printer-mobile-units/getFromHost GET
        const response2 = await api('/api/single-printer-mobile-units/getFromHost', 'get')
        if (response2) {
            singlePrinterMobileUnitDefined.value = true
            localPrint.value = response2.localPrint
            printerMobileUnitName.value = response2.name
            currentId.value = response2.id
            if (localPrint.value === false) selectedPrinter.value = response2.printer['@id']
            else selectedPrinter.value = null
        }
    })
</script>

<template>
    <div v-if="!singlePrinterMobileUnitDefined">
        <div v-if="!isPosteConfigurationChangeNeed" class="bg-danger text-center text-white">
            Ce poste n'est pas reconnu ou son IP a changé<br/>
            Impossible de poursuivre tant qu'une imprimante ne lui aura été associée
        </div>
        <div v-else class="bg-info text-center text-white">
            Pour modifier les données de ce poste veuillez modifier les données ci-dessous et enregistrer
        </div>
        <div class="container">
            <div class="row">
                <div class="col-6 offset-3 p-2">
                    <div class="input-group m-2">
                        <span class="input-group-text">Nom du poste</span>
                        <input v-model="temporaryPosteName" class="form-control" placeholder="Nom du poste" type="text"/>
                    </div>
                    <div class="input-group m-2">
                        <span class="input-group-text">Imprimante locale</span>
                        <AppSwitch
                            id="localOrNetWorkPrint"
                            class="m-2"
                            :disabled="false"
                            :field="{label: 'Imprimante Local/Réseau', name: 'localPrint', type: 'boolean'}"
                            form=""
                            :model-value="localPrint"
                            @update:model-value="updateLocalPrint"/>
                    </div>
                    <div v-if="!localPrint" class="input-group m-2">
                        <span class="input-group-text">Sélectionner l'imprimante réseau</span>
                        <CustomeSelect
                            class="custome-select"
                            :options="printers"
                            @update:model-value="onNetworkPrinterSelected"/>
                    </div>
                    <button class="btn btn-success m-2" @click="lierImprimante">
                        <Fa :brand="false" icon="save"/> Enregistrer
                    </button>
                    <div v-if="initPosteError !== null" class="bg-light text-danger">
                        {{ initPosteError }}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div v-else>
        <div v-if="selectedPrinter !== null" class="bg-info text-center text-white">
            <strong>Poste:</strong> {{ printerMobileUnitName }} <strong>Impression Réseau vers:</strong> {{ selectedPrinterName }}
            <Fa :brand="false" class="btn btn-primary m-1" icon="gear" title="Changer la configuation du poste" @click="onPosteConfigurationChange"/>
        </div>
        <div v-else class="bg-info text-center text-white">
            <strong>Poste:</strong> {{ printerMobileUnitName }} <strong>Impression locale</strong>
            <Fa :brand="false" class="m-2 text-primary icon-mode icon-link icon-link-hover" icon="gear" title="Changer la configuation du poste" @click="onPosteConfigurationChange"/>
        </div>
        <div class="carton-label container-fluid">
            <div class="row">
                <div class="col-12">
                    <ul>
                        <AppItemCarte label="BDD :" :value="originGP ? 'GP' : 'Antenne'"/>
                    </ul>
                </div>
                <div class="col-5">
                    <ul>
                        <AppItemCarte
                            label="Famille">
                            <Fa
                                v-if="modeleEtiquette.templateFamily === 'carton'"
                                :brand="false"
                                class="color-carton font-size-15px"
                                icon="box-open"/>
                            <span v-else>{{ modeleEtiquette.templateFamily }}</span>
                        </AppItemCarte>
                        <AppItemCarte label="Exp." :value="manufacturer"/>
                        <AppItemCarte label="Matr." :value="productRefAndIndice"/>
                        <AppItemCarte label="OF" :value="ofNumberAndIndice"/>
                    </ul>
                </div>
                <div class="col-7">
                    <ul>
                        <AppItemCarte label="Format :" :value="modeleEtiquette.labelKind"/>
                        <AppItemCarte label="Dest. :" :value="of.data ? of.data.customerName : ''"/>
                        <AppItemCarte label="Opérateur :" :value="operateur.name"/>
                        <AppItemCarte label="Produit :" :value="productRefAndIndice"/>
                    </ul>
                </div>
                <div v-if="currentStep > 2" class="col-12">
                    <span class="text-center font-50px fw-bold d-block">
                        {{ nbProduit }}/{{ of.data.productConditionnement }}
                    </span>
                </div>
            </div>
        </div>
        <AppStepProgress :current-step="currentStep" :steps="steps"/>
        <div class="step-forms">
            <AppStepBase
                v-if="currentStep === 0"
                class="form-step"
                @next-step="getBase"/>
            <AppStepOperateur
                v-if="currentStep === 1"
                class="form-step"
                @next-step="getOperateur"/>
            <AppStepOrderFabrication
                v-if="currentStep === 2"
                class="form-step"
                :origin-g-p="originGP"
                @next-step="getOf"/>
            <AppStepProduit
                v-if="currentStep === 3"
                class="form-step"
                :modele-etiquette="modeleEtiquette"
                :of="of"
                :operateur="operateur"
                @change-products="nbProduit = $event"
                @next-step="getProducts"/>
            <AppStepPrint
                v-if="currentStep === 4"
                class="form-step"
                :local-print="localPrint"
                :modele-etiquette="modeleEtiquette"
                :nb-produit="nbProduit"
                :of="of"
                :operateur="operateur"
                :printer="selectedPrinter"
                :products="products"
                @next-step="onPrinted"/>
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
    </div>
</template>

<style scoped>
    .font-50px {
        font-size: 50px;
    }
    .custome-select {
        width: 100px;
        width:100%
    }
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
