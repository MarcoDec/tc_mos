<script setup>
    import {defineEmits, defineProps, onMounted, ref} from 'vue'
    import api from '../../../../../api'

    const emits = defineEmits(['nextStep'])
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
    const file = new Blob([zpl.value], {type: 'text/plain'})
    zplHref.value = URL.createObjectURL(file)

    const selectedPrinter = ref(null)

    function imprimeReseau() {
        api(`/api/label-cartons/${props.products.label.id}/print`, 'get')
            .then(() => {
                printerLaunched.value = true
                emits('nextStep')
            })
    }
    const printerLaunched = ref(false)
    onMounted(() => {
        imprimeReseau()
    })
</script>

<template>
    <div>
        <div class="step-title bg-info text-center" style="width: 100%; border-radius: 10px 10px 0px 0px; font-weight: bold;">Impression</div>
        <div class="text-center">
            Aperçu de l'étiquette
            <img id="imageToPrint" class="toPrint" :src="imageUrl" alt="aperçu etiquette" width="280"/>
        </div>
        <div v-if="!printerLaunched"><span class="spinner-border" role="status"/>Lancement impression</div>
        <div v-if="printerLaunched" class="bg-success text-white">Impression lancée !</div>
    </div>
</template>

<style scoped>

</style>