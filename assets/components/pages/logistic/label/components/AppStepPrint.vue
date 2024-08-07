<script setup>
    import {defineEmits, defineProps, onMounted, ref} from 'vue'
    import api from '../../../../../api'

    const emits = defineEmits(['nextStep'])
    const props = defineProps({
        products: {default: () => ({}), required: true, type: Object},
        localPrint: {default: () => true, required: true, type: Boolean}
    })
    const zpl = ref(props.products.zpl)
    const zplHref = ref('')
    const imageUrl = ref(props.products.imageUrl)
    const file = new Blob([zpl.value], {type: 'text/plain'})
    const printerLaunched = ref(false)
    zplHref.value = URL.createObjectURL(file)
    function calculateDPI() {
        // Créer un élément div temporaire
        const div = document.createElement('div')
        div.style.height = '1in' // Hauteur en pouces
        div.style.width = '1in' // Largeur en pouces
        div.style.top = '-100%' // Placer le div hors de l'écran
        div.style.left = '-100%'
        div.style.position = 'absolute'
        // Ajouter le div au body
        document.body.appendChild(div)
        // Mesurer le nombre de pixels dans un pouce
        const dpi = div.offsetWidth
        // Supprimer le div
        document.body.removeChild(div)
        return dpi
    }

    const screenDPI = calculateDPI()
    //console.log('DPI de l\'écran :', screenDPI)
    //imprimantes locales : 203dpi => 8dpmm
    const dpiImprimante = 203
    const ratio = dpiImprimante / screenDPI
    const labelWidth = props.products.label.labelKind === 'TConcept' ? 4 * dpiImprimante / ratio : 3 * dpiImprimante / ratio
    const labelHeight = props.products.label.labelKind === 'TConcept' ? 6 * dpiImprimante / ratio : 8.3 * dpiImprimante / ratio

    function imprimeReseau() {
        api(`/api/label-cartons/${props.products.label.id}/print`, 'get')
            .then(() => {
                printerLaunched.value = true
                emits('nextStep')
            })
    }
    function imprimeLocal() {
        window.print()
        printerLaunched.value = true
        emits('nextStep')
    }
    onMounted(() => {
        if (props.localPrint === false) {
            imprimeReseau()
        }
    })
    function onImageLoaded() {
        if (props.localPrint) {
            imprimeLocal()
        }
    }
</script>

<template>
    <div>
        <div class="bg-info step-title text-center">
            Impression
        </div>
        <div class="text-center">
            Aperçu de l'étiquette
            <img id="imageToPrint" class="toPrint" :src="imageUrl" alt="aperçu etiquette" :width="labelWidth" :height="labelHeight" @load="onImageLoaded"/>
        </div>
        <div v-if="!printerLaunched">
            <span class="spinner-border" role="status"/>
            Lancement impression
        </div>
        <div v-if="printerLaunched" class="bg-success text-white">
            Impression lancée !
        </div>
    </div>
</template>

<style scoped>
    .step-title {
        width: 100%;
        border-radius: 10px 10px 0px 0px;
        font-weight: bold;
    }
</style>
