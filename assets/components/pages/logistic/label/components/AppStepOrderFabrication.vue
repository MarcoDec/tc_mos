<script setup>
    import {defineEmits, onMounted, ref} from 'vue'

    const emits = defineEmits(['nextStep'])
    const of = ref('<à définir>')
    const ofField = ref('')
    const inputOfRef = ref(null)

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
        const ofNumber = ofField.value.split('/')[2].split('.')[0]
        const ofIndice = ofField.value.split('/')[2].split('.')[1]
        // On regarde si on trouve l'OF coté GP
        let testGP = false
        let testAntenne = false
        let error = false
        const GPresponse = await fetch(`http://gp.tconcept.local/dist/api/orderfabrication.php?action=show&ofNumber=${ofNumber}&ofIndice=${ofIndice}`)
        if (GPresponse.ok) {
            const GPJson = await GPresponse.json()
            if (GPJson === 'null') testGP = false
            else {
                of.value = {status: true, data: GPJson}
                testGP = true
            }
        } else {
            //erreur
            error = true
            console.error(`HTTP-Error GP: ${GPresponse.status}`)
        }
        if (!testGP) {
            // On regarde si on trouve l'OF coté Antenne
            const Antenneresponse = await fetch(`http://antenne.tconcept.local/dist/api/orderfabrication.php?action=show&ofNumber=${ofNumber}&ofIndice=${ofIndice}`)
            if (Antenneresponse.ok) {
                const AntenneJson = await Antenneresponse.json()
                if (AntenneJson === 'null') testAntenne = false
                else {
                    of.value = {status: true, data: AntenneJson}
                    testAntenne = true
                }
            } else {
                //erreur
                error = true
                console.error(`HTTP-Error Antenne: ${Antenneresponse.status}`)
            }
            if (!testAntenne) {
                // On a rien trouvé
                of.value = {status: false}
            }
        }
        // si testGP Ok alors on charge coté GP le produit lié via la propriété de of nommée id_product
        if (testGP) {
            const productResponse = await fetch(`http://gp.tconcept.local/dist/api/product.php?action=show&id=${of.value.data.id_product}`)
            if (productResponse.ok) {
                const productJson = await productResponse.json()
                if (productJson === 'null') {
                    //erreur
                    error = true
                    console.error('Il n\'y a pas de produit lié à cet OF GP coté GP')
                } else of.value.data = {
                    ...of.value.data,
                    productRef: productJson.ref,
                    productIndice: productJson.indice,
                    productDescription: productJson.designation
                }
            } else {
                //erreur
                error = true
                console.error(`HTTP-Error GP: ${productResponse.status}`)
            }
        } else if (testAntenne) {
            // si testAntenne Ok alors on charge coté Antenne le produit lié via la propriété de of nommée id_product
            const productResponse = await fetch(`http://antenne.tconcept.local/dist/api/product.php?action=show&id=${of.value.data.id_product}`)
            if (productResponse.ok) {
                const productJson = await productResponse.json()
                if (productJson === 'null') {
                    //erreur
                    error = true
                    console.error('Il n\'y a pas de produit lié à cet OF Antenne coté Antenne')
                } else of.value.data = {
                    ...of.value.data,
                    productRef: productJson.ref,
                    productIndice: productJson.indice,
                    productDescription: productJson.designation
                }
            } else {
                //erreur
                error = true
                console.error(`HTTP-Error Antenne: ${productResponse.status}`)
            }
        }
        return !error && (testGP || testAntenne)
    }
    async function validate() {
        const result = await getOf()
        if (result) {
            //console.log('validate ok', of.value)
            emits('nextStep', of.value)
        } else {
            alert('Veuillez scanner l\'OF')
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
            <div class="d-flex flex-column">
                <div class="align-items-center d-flex flex-row justify-content-end">
                    <label for="of" class="labelOfProduct">
                        <strong>
                            OF
                        </strong>
                    </label>
                    <input id="of" ref="inputOfRef" v-model="ofField" class="form-control inputOfProduct" type="text"/>
                </div>
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
    .labelOfProduct {
        font-size: 2rem;
        margin-right: 1rem;
    }

    .inputOfProduct {
        font-size: 2rem;
        width: 20rem;
    }

    .step-title {
        font-size: 3rem;
        margin-bottom: 1rem;
    }
</style>
