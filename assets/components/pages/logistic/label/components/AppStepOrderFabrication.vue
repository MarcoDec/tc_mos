<script setup>
    import {defineEmits, onMounted, ref} from 'vue'

    const emits = defineEmits(['nextStep'])
    const props = defineProps({
        originGP: {default: true, required: true, type: Boolean}
    })
    const of = ref('<à définir>')
    const ofField = ref('')
    const inputOfRef = ref(null)

    async function getOrdreDeFabrication(originGP, ofNumber, ofIndice) {
        const baseUrl = originGP ? 'http://gp.tconcept.local/dist/api/orderfabrication.php' : 'http://antenne.tconcept.local/dist/api/orderfabrication.php'
        const response = await fetch(`${baseUrl}?action=show&ofNumber=${ofNumber}&ofIndice=${ofIndice}`)
        if (response.ok) {
            const json = await response.json()
            if (json === 'null') {
                of.value = {status: false}
                return false
            } else {
                of.value = {status: true, data: json}
                return true
            }
        } else {
            console.error(`HTTP-Error: ${response.status}`)
            return false
        }
    }
    async function getProduct(originGP, idProduct) {
        const baseUrl = originGP ? 'http://gp.tconcept.local/dist/api/product.php' : 'http://antenne.tconcept.local/dist/api/product.php'
        const response = await fetch(`${baseUrl}?action=show&id=${idProduct}`)
        if (response.ok) {
            const json = await response.json()
            if (json === 'null') {
                of.value = {status: false}
                return false
            } else {
                of.value.data = {
                    ...of.value.data,
                    product: json,
                    productRef: json.ref,
                    productIndice: json.indice,
                    productDescription: json.designation,
                    productConditionnement: json.conditionnement,
                    productLabelLogo: json.labelLogo,
                    customerId: json.id_customer
                }
                return true
            }
        } else {
            console.error(`HTTP-Error: ${response.status}`)
            return false
        }
    }

    async function getCustomer(originGP, idCustomer) {
        const baseUrl = originGP ? 'http://gp.tconcept.local/dist/api/customerController.php' : 'http://antenne.tconcept.local/dist/api/customerController.php'
        const response = await fetch(`${baseUrl}?action=get&id=${idCustomer}`)
        if (response.ok) {
            const json = await response.json()
            if (json === 'null') {
                of.value = {status: false}
                return false
            } else {
                of.value.data = {
                    ...of.value.data,
                    customer: json,
                    customerName: json.nom
                }
                console.log('customer data chargées', of.value.data.customer)
                return true
            }
        } else {
            console.error(`HTTP-Error: ${response.status}`)
            return false
        }
    }

    async function getOf() {
        //region explications
        // On tente de récupérer l'OF depuis les informations du code barre
        // en regardant si l'OF est définit coté GP ou Antenne
        // la route de récupération coté gp sont:
        // http://gp.tconcept.local/dist/api/orderfabrication.php?action=show&id=3
        // la route de récupération coté antenne est:
        // http://antenne.tconcept.local/dist/api/orderfabrication.php?action=show&id=3
        // Le code barre scannée attendu est de la forme: productRef/productIndice/ofNumber.ofIndice
        // On ne cherche à recupérer que l'ofNumber et l'ofIndice
        //endregion
        const ofNumber = ofField.value.split('.')[0]
        const ofIndice = ofField.value.split('.')[1]
        const resultOFOK = await getOrdreDeFabrication(props.originGP, ofNumber, ofIndice)
        let error = false
        // On vérifie que l'OF récupéré est valide
        if (resultOFOK && of.value.data.statut === "1" || ['1', '2', '5', '6', '7'].indexOf(of.value.data.id_orderfabricationstatus) > 0) {
            //erreur
            error = true
            console.error('L\'OF n\'est pas valide', of.value.data)
            return false
        }

        // si resultOFOK Ok alors on charge coté GP le produit lié via la propriété de of nommée id_product
        if (resultOFOK && !error) {
            const resultProductOK = await getProduct(props.originGP, of.value.data.id_product)
            if (!resultProductOK) {
                //erreur
                error = true
                console.error('Il n\'y a pas de produit lié à l\'OF')
                return false
            } else {
                // Si resultProductOK Ok alors on charge les données client
                const resultCustomerOK = await getCustomer(props.originGP, of.value.data.customerId)
                if (!resultCustomerOK) {
                    //erreur
                    error = true
                    console.error('Il n\'y a pas de client lié au produit')
                    return false
                } else {
                    // Si resultCustomerOK Ok alors on retourne true
                    return true
                }
            }

        }
    }
    async function validate() {
        const result = await getOf()
        if (result) {
            //console.log('validate ok', of.value)
            emits('nextStep', of.value)
        } else {
            alert('OF ou Produit associé ou Client associé non valide')
        }
    }
    onMounted(() => {
        inputOfRef.value.focus()
        inputOfRef.value.select()
    })
</script>

<template>
    <div>
        <div class="step-title">
            Entrer l'Ordre de Fabrication
        </div>
        <div class="align-items-center d-flex flex-row justify-content-end">
            <div class="align-items-center d-flex flex-row justify-content-end">
                <label for="of" class="labelOfProduct">
                    <strong>
                        OF
                    </strong>
                </label>
                <input
                    id="of"
                    ref="inputOfRef"
                    v-model="ofField"
                    class="form-control inputOfProduct"
                    type="text"
                    @keyup.enter="validate"/>
            </div>
            <button class="btn btn-success height-80 m-2" @click="validate">
                <Fa :brand="false" icon="chevron-right"/>
            </button>
        </div>
    </div>
</template>

<style scoped>
    .height-80 {
        height: 80%;
    }
    .width-100px {
        width: 100px;
    }
</style>
