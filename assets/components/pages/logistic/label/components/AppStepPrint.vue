<script setup>
    import {defineProps, onMounted, ref} from 'vue'
    import api from '../../../../../api'
    import CustomeSelect from './CustomeSelect.vue'

    const props = defineProps({
        modeleEtiquette: {default: {}, required: true, type: Object},
        of: {default: {}, required: true, type: Object},
        operateur: {default: {}, required: true, type: Object},
        nbProduit: {default: 0, required: true, type: Number},
        products: {default: {}, required: true, type: Object}
    })

    const zpl = ref(props.products.zpl)
    const zplHref = ref('')
    const imageUrl = ref(props.products.imageUrl)
    const newLabel = ref(props.products.label)
    const file = new Blob([zpl.value], {type: 'text/plain'})
    zplHref.value = URL.createObjectURL(file)

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
    const searchPrinter = ref(true)
    const printerNotYetFound = ref(true)
    onMounted(() => {

        setTimeout(() => {
            searchPrinter.value = false
            printerNotYetFound.value = false
        }, 2000)
    })
</script>

<template>
    <div>
        <div class="step-title bg-info text-center" style="width: 100%; border-radius: 10px 10px 0px 0px; font-weight: bold;">Impression</div>
        <p
            v-if="searchPrinter"
            class="m-2 d-flex justify-content-center align-items-center">
            Recherche existence d'une imprimante associée au poste
        </p>
        <span
            v-if="searchPrinter && printerNotYetFound"
            class="spinner-border m-2"
            role="status"/>
        <div class="text-success">Imprimante trouvée !</div>
        <div>
            Aperçu de l'étiquette
            <img id="imageToPrint" class="toPrint" :src="imageUrl" alt="aperçu etiquette" width="280"/>

        </div>
        <div><span class="spinner-border" role="status"/>Lancement impression</div>
        <div class="bg-danger text-white">Erreur d'impression</div>


        <div class="bg-success text-white">Association de l'imprimante réussie</div>
        <div class="text-danger">
            Aucune imprimante trouvée
            <div class="d-flex flex-row align-items-stretch align-self-stretch justify-content-center">
                <a :href="zplHref" download="etiquette.zpl" class="btn btn-info d-flex justify-content-center min-button">
                    <Fa :brand="false" icon="download"/> ZPL
                </a>
            </div>

            <div class="align-items-stretch align-self-stretch d-flex flex-row justify-content-center mt-4">
                <button class="align-items-center btn btn-success d-flex d-inline-block flex-column justify-content-center min-button" @click="imprimeLocal">
                    <Fa :brand="false" icon="print"/> Local
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>

</style>