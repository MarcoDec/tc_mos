<script setup>
    import {defineProps, ref} from 'vue'
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
    const printers = ref([])
    const file = new Blob([zpl.value], {type: 'text/plain'})
    zplHref.value = URL.createObjectURL(file)
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
</script>

<template>
    <div>
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
</template>

<style scoped>

</style>