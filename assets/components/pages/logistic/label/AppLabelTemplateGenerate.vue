<script setup>
    import {computed, onMounted, ref} from 'vue'
    import {useRoute, useRouter} from 'vue-router'
    import useUser from '../../../../stores/security'
    import api from '../../../../api'
    import AppItemCarte from './components/AppItemCarte.vue'
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

    response.then(data => {
        modeleEtiquette.value = data
    })
    const operateur = ref({ name: '<à définir>'})
    const currentStep = ref(1)

    function getOperateur(operateurData) {
        console.log('operateurData', operateurData)
        operateur.value = operateurData
        currentStep.value += 1
    }
    const of = ref({})
    function getOf(ofData) {
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
    function onPrinted() {
        console.log('onPrinted')
        currentStep.value += 1
    }

    const printers = ref([])
    function getPrinters() {
        api('/api/printers', 'get')
            .then(data => {
                printers.value = data['hydra:member']
            })
    }
    getPrinters()
    const selectedPrinterName = computed(() => {
        return printers.value.find(printer => printer['@id'] === selectedPrinter.value)['name']
    })
    const temporaryPrinter = ref(null)
    function onNetworkPrinterSelected(printer) {
        temporaryPrinter.value = printer
    }
    const temporaryPosteName = ref(null)
    function lierImprimante() {
        if (temporaryPrinter.value === null) {
            return
        }
        const data = {
            printer: temporaryPrinter.value['@id'],
            name : temporaryPosteName.value
        }
        api('/api/single-printer-mobile-units/addNewAndLink', 'post', data)
            .then(() => {
                window.location.reload
            })
    }
    onMounted(async () => {
        if (!currentUser.isLogged) router.push({name: 'login'})
        if (!currentUser.isLogisticsWriter || !currentUser.isProductionWriter) router.push({name: 'home'})
        //Récupération de l'imprimante associée au poste
        // via l'api /api/single-printer-mobile-units/getFromHost GET
        const response = await api('/api/single-printer-mobile-units/getFromHost', 'get')
        if (response) {
            const printer = response.printer['@id']
            if (printer) {
                console.log('printer', printer)
                selectedPrinter.value = printer
            } else {
                console.error('printer not found')
            }
        }
    })
</script>

<template>
    <div v-if="selectedPrinter !== null" class="bg-success text-white text-center">
        Imprimante associée au poste courant trouvée `{{ selectedPrinterName }}`
    </div>
    <div v-if="selectedPrinter === null" class="bg-danger text-white text-center">
        Aucune imprimante n'est associé au poste courant<br>
        Impossible de poursuivre tant qu'une imprimante n'aura pas été associée
    </div>
    <div v-if="selectedPrinter !== null" class="carton-label container-fluid">
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
    <AppStepProgress v-if="selectedPrinter !== null" :current-step="currentStep" :steps="steps"/>
    <div v-if="selectedPrinter !== null" class="step-forms">
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
        <AppStepPrint
            v-if="currentStep === 4"
            class="form-step"
            :modele-etiquette="modeleEtiquette"
            :of="of"
            :operateur="operateur"
            :nb-produit="nbProduit"
            :products="products"
            :printer="selectedPrinter"
            @next-step="onPrinted"
        />
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
    <div v-if="selectedPrinter === null" class="container">
        <div class="row">
            <div class="offset-3 col-6 p-2">
                <div class="input-group m-2">
                    <span class="input-group-text">Nom du poste</span>
                    <input class="form-control" placeholder="Nom du poste" type="text" v-model="temporaryPosteName"/>
                </div>
                <div class="input-group m-2">
                    <span class="input-group-text">Sélectionner l'imprimante réseau</span>
                    <CustomeSelect
                        :options="printers"
                        @update:model-value="onNetworkPrinterSelected"
                        style="width: 100px; width:100%"
                    />
                </div>
                <button class="btn btn-success m-2" @click="lierImprimante">
                    <Fa :brand="false" icon="save"/> Enregistrer
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
