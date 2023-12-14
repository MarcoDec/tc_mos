<script setup>
    import {onMounted, ref} from 'vue'
    import {useRoute, useRouter} from 'vue-router'
    import api from '../../../../api'
    import AppItemCarte from './components/AppItemCarte.vue'
    import AppStepProgress from './components/AppStepProgress.vue'
    import CustomeSelect from './components/CustomeSelect.vue'

    const inputOperateurRef = ref(null)
    const inputOfRef = ref(null)
    const inputProduitRef = ref(null)
    const route = useRoute()
    const router = useRouter()
    const idLabelTemplate = route.params.idLabelTemplate
    const modeleEtiquette = ref({})
    const response = api(`/api/label-templates/${idLabelTemplate}`, 'get')
    const operateur = ref('<à définir>')
    const of = ref('<à définir>')
    const nbProduit = ref(0)
    const currentStep = ref(1)
    const product = ref('')
    const zpl = ref('')
    const zplHref = ref('')
    const imageUrl = ref('')
    const steps = ref([
        {
            id: 1,
            label: 'Opérateur',
            check() {
                return operateur.value !== '<à définir>'
            },
            validate() {
                if (this.check()) currentStep.value += 1
                else alert('Veuillez scanner le badge de l\'opérateur')
            }
        },
        {
            id: 2,
            label: 'OF',
            check() {
                return of.value !== '<à définir>'
            },
            validate() {
                if (this.check()) currentStep.value += 1
                else alert('Veuillez scanner l\'OF')
            }
        },
        {
            id: 3,
            label: 'Scan Produits',
            check() {
                if (nbProduit.value === 0) {
                    alert('Veuillez scanner au moins un produit')
                    return false
                }
                if (this.scannedProducts.every(val => val === this.scannedProducts[0])) return true
                alert('Les produits scannés ne sont pas tous identiques, veuillez recommencer depuis le début')
                this.scannedProducts = []
                return false
            },
            scannedProducts: [],
            next() {
                if (product.value === '') return
                if (this.scannedProducts.length === 0) {
                    this.scannedProducts.push(product.value)
                    nbProduit.value = this.scannedProducts.length
                    product.value = ''
                    return
                }
                if (this.scannedProducts.every(val => val === product.value)) {
                    this.scannedProducts.push(product.value)
                    nbProduit.value = this.scannedProducts.length
                    product.value = ''
                    return
                }
                alert('Le dernier produit scannés est différent des précédents, veuillez recommencer')
                product.value = ''
            },
            validate() {
                if (this.check()) {
                    //Avant de passer à l'impression il faut créer l'étiquette avec l'ensemble des données
                    const dataTosend = {
                        labelKind: modeleEtiquette.value.labelKind,
                        labelName: modeleEtiquette.value.labelName,
                        manufacturer: modeleEtiquette.value.manufacturer,
                        customerAddressName: modeleEtiquette.value.customerAddressName,
                        productDescription: modeleEtiquette.value.productDescription,
                        productReference: modeleEtiquette.value.productReference,
                        productIndice: modeleEtiquette.value.productIndice,
                        templateFamily: modeleEtiquette.value.templateFamily,
                        operator: operateur.value,
                        batchnumber: of.value,
                        quantity: nbProduit.value
                    }
                    const response = api('/api/label-cartons', 'post', dataTosend)
                    // et récupérer le code ZPL
                    response.then(data => {
                        var file = new Blob([data.zpl], {type: 'text/plain'})
                        zplHref.value = URL.createObjectURL(file)
                        // http://api.labelary.com/v1/printers/{dpmm}/labels/{width}x{height}/{index}/{zpl}
                        const dpmm = '8dpmm'
                        const width = modeleEtiquette.value.width
                        const height = modeleEtiquette.value.height
                        const index = '0'
                        imageUrl.value = `http://api.labelary.com/v1/printers/${dpmm}/labels/${width}x${height}/${index}/${data.zpl}`
                        currentStep.value += 1
                    })
                    // avant de passer à l'étape suivante
                }
                else alert('Veuillez scanner au moins un produit')
            },
            reset() {
                this.scannedProducts = []
                nbProduit.value = 0
                product.value = ''
            }
        },
        {id: 4, label: 'Impression', icon: 'print'}
    ])
    response.then(data => {
        modeleEtiquette.value = data
    })
    onMounted(() => {
        inputOperateurRef.value.focus()
        inputOperateurRef.value.select()
    })
    function changeTemplate() {
        router.push({name: 'label-template-list'})
    }
    function resetAll() {
        currentStep.value = 1
        operateur.value = '<à définir>'
        of.value = '<à définir>'
        nbProduit.value = 0
        product.value = ''
        inputOperateurRef.value.focus()
        inputOperateurRef.value.select()
    }
    function restartFromOf() {
        currentStep.value = 2
        of.value = '<à définir>'
        nbProduit.value = 0
        product.value = ''
        inputOfRef.value.focus()
        inputOfRef.value.select()
    }
    function restartNewCarton() {
        currentStep.value = 3
        steps[2].scannedProducts= []
        nbProduit.value = 0
        product.value = ''
        inputProduitRef.value.focus()
        inputProduitRef.value.select()
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
            printer: selectedPrinter.value.name,
            zpl: zpl.value
        }
        api('/api/print-zpl', 'post', data)
            .then(() => {
                alert('Impression lancée')
            })
    }
</script>

<template>
    <div class="carton-label">
        <ul>
            <AppItemCarte
                label="Famille d'étiquette :">
                <Fa
                    v-if="modeleEtiquette.templateFamily === 'carton'"
                    :brand="false"
                    class="color-carton font-size-15px"
                    icon="box-open"/>
            </AppItemCarte>
            <AppItemCarte label="Format d'étiquette :" :value="modeleEtiquette.labelKind"/>
            <AppItemCarte label="Nom du modèle :" :value="modeleEtiquette.labelName"/>
            <AppItemCarte label="Fabricant :" :value="modeleEtiquette.manufacturer"/>
            <AppItemCarte label="Site Livraison Client :" :value="modeleEtiquette.customerAddressName"/>
            <AppItemCarte label="Libellé Produit :" :value="modeleEtiquette.productDescription"/>
            <AppItemCarte class="bg-info" label="Référence Produit :" :value="`${modeleEtiquette.productReference}-${modeleEtiquette.productIndice}`"/>
            <AppItemCarte label="Opérateur :" :value="operateur"/>
            <AppItemCarte label="OF :" :value="of"/>
            <AppItemCarte label="Nb Produit scanné :" :value="`${nbProduit}`"/>
        </ul>
    </div>
    <AppStepProgress :current-step="currentStep" :steps="steps"/>
    <div class="step-forms">
        <div v-show="currentStep === 1" class="form-step">
            <div class="step-title">Scanner le badge de l'opérateur</div>
            <div class="d-flex flex-row align-items-baseline align-self-stretch">
                <input id="operateur" ref="inputOperateurRef" v-model="operateur" class="form-control m-2" type="text"/>
                <button class="btn btn-success m-2" @click="steps[0].validate()">
                    <Fa :brand="false" icon="chevron-right"/>
                </button>
            </div>
        </div>
        <div v-show="currentStep === 2" class="form-step">
            <div class="step-title">Scanner l'OF'</div>
            <div class="d-flex flex-row align-items-baseline align-self-stretch">
                <input id="of" ref="inputOfRef" v-model="of" class="form-control m-2" type="text"/>
                <button class="btn btn-success m-2" @click="steps[1].validate()">
                    <Fa :brand="false" icon="chevron-right"/>
                </button>
            </div>
        </div>
        <div v-show="currentStep === 3" class="form-step">
            <div class="step-title">Scan Produits</div>
            <div class="d-flex flex-row align-items-stretch align-self-stretch justify-content-between">
                <input id="product" ref="inputProduitRef" v-model="product" class="form-control m-2" type="text"/>
                <button class="btn btn-success m-2" @click="steps[2].next()">
                    <Fa :brand="false" icon="plus"/>
                </button>
            </div>
            <div class="d-flex flex-row align-items-stretch align-self-stretch justify-content-between mt-3">
                <button class="btn btn-warning d-inline-block m-2" @click="steps[2].reset()" title="Recommencer">
                   <Fa :brand="false" icon="backward-step"/> Recommencer les scans
                </button>
                <button class="btn btn-success d-inline-block m-2" @click="steps[2].validate()">
                    <Fa :brand="false" icon="chevron-right"/>
                </button>
            </div>
        </div>
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

            <div class="d-flex flex-row align-items-stretch align-self-stretch justify-content-center mt-4">
                <button class="btn btn-success d-inline-block d-flex flex-column justify-content-center min-button align-items-center" @click="imprimeLocal">
                    <Fa :brand="false" icon="print"/> Local
                </button>
            </div>
        </div>
        <div v-show="currentStep >= 5" class="form-step">
            <div class="step-title">Choix</div>
            <div class="d-flex flex-column align-items-center">
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
        max-width: 300px;
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
</style>
