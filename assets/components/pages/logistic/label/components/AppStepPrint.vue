<script setup>
    import {defineEmits, defineProps, onMounted, ref} from 'vue'
    import api from '../../../../../api'

    const emits = defineEmits(['nextStep'])
    const props = defineProps({
        products: {default: () => ({}), required: true, type: Object}
    })
    const zpl = ref(props.products.zpl)
    const zplHref = ref('')
    const imageUrl = ref(props.products.imageUrl)
    const file = new Blob([zpl.value], {type: 'text/plain'})
    zplHref.value = URL.createObjectURL(file)

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
        <div class="bg-info step-title text-center">
            Impression
        </div>
        <div class="text-center">
            Aperçu de l'étiquette
            <img id="imageToPrint" class="toPrint" :src="imageUrl" alt="aperçu etiquette" width="280"/>
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
