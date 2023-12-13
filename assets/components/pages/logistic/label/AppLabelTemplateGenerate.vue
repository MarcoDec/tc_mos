<script setup>
    import {onMounted, ref} from 'vue'
    import {useRoute} from 'vue-router'
    import api from '../../../../api'
    import AppItemCarte from './components/AppItemCarte.vue'
    import AppStepProgress from './components/AppStepProgress.vue'

    const inputOperateurRef = ref(null)
    const inputOfRef = ref(null)
    const inputProduitRef = ref(null)
    const route = useRoute()
    const idLabelTemplate = route.params.idLabelTemplate
    console.log('idLabelTemplate', idLabelTemplate)
    const modeleEtiquette = ref({})
    const response = api(`/api/label-templates/${idLabelTemplate}`, 'get')
    const operateur = ref('<à définir>')
    const of = ref('<à définir>')
    const nbProduit = ref(0)
    const currentStep = ref(1)
    const product = ref('')
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
                this.scannedProducts.push(product.value)
                nbProduit.value = this.scannedProducts.length
                product.value = ''
            },
            validate() {
                if (this.check()) currentStep.value += 1
                else alert('Veuillez scanner au moins un produit')
            }
        },
        {id: 4, label: 'Impression', icon: 'print'}
    ])
    response.then(data => {
        modeleEtiquette.value = data
        console.log('modeleEtiquette', modeleEtiquette.value)
    })
    onMounted(() => {
        inputOperateurRef.value.focus()
        inputOperateurRef.value.select()
    })
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
            <AppItemCarte label="Nb Produit scanné :" :value="nbProduit"/>
        </ul>
    </div>
    <AppStepProgress :current-step="currentStep" :steps="steps"/>
    <div class="step-forms">
        <div v-show="currentStep === 1" class="form-step">
            <div>Scanner le badge de l'opérateur</div>
            <input id="operateur" ref="inputOperateurRef" v-model="operateur" class="form-control" type="text"/>
            <button class="btn btn-success mt-2" @click="steps[0].validate()">
                Suivant
            </button>
        </div>
        <div v-show="currentStep === 2" class="form-step">
            <div>Scanner l'OF'</div>
            <input id="of" ref="inputOfRef" v-model="of" class="form-control" type="text"/>
            <button class="btn btn-success mt-2" @click="steps[1].validate()">
                Suivant
            </button>
        </div>
        <div v-show="currentStep === 3" class="form-step">
            <div>Scan Produits</div>
            <input id="product" ref="inputProduitRef" v-model="product" class="form-control" type="text"/>
            <div>
                <button class="btn btn-success d-inline-block m-2" @click="steps[2].next()">
                    Suivant
                </button>
                <button class="btn btn-success d-inline-block m-2" @click="steps[2].validate()">
                    Terminer
                </button>
            </div>
            <ul class="font-size-8px">
                <li v-for="(aProduct, index) in steps[2].scannedProducts" :key="`${aProduct}-${index}`" class="d-inline-block m-1">
                    {{ aProduct }},
                </li>
            </ul>
        </div>
    </div>
</template>

<style scoped>
    .font-size-8px {
        font-size: 8px;
    }
    .step-forms {
        display: flex;
        justify-content: center;
        margin: 20px 0;
    }
    .step-forms .form-step {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        width: 300px;
        height: 150px;
        border: 1px solid black;
        border-radius: 10px;
    }
    .carton-label {
        font-family: 'Arial', sans-serif;
        font-size: 10px;
        max-width: 300px;
        margin: 20px auto;
        padding: 20px;
        background-color: #f8f8f8;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .carton-label ul {
        list-style-type: none;
        padding: 0;
        margin: 0;
    }

    .carton-label li {
        //padding: 8px 0;
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
</style>
