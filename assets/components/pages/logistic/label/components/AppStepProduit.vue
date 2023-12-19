<script setup>
    import {defineEmits, defineProps, ref} from 'vue'
    import api from '../../../../../api'
    const emits = defineEmits(['changeProducts', 'nextStep'])
    const props = defineProps({
        modeleEtiquette: {default: () => ({}), required: true, type: Object},
        of: {default: () => ({}), required: true, type: Object},
        operateur: {default: () => ({}), required: true, type: Object}
    })
    const checkResult = ref({
        class: '',
        text: '',
        state: true
    })
    const product = ref('<à définir>')
    const inputProduitRef = ref(null)
    const scannedProducts = ref([])
    const nbProduit = ref(0)

    function check() {
        if (nbProduit.value === 0) {
            alert('Veuillez scanner au moins un produit')
            return false
        }
        if (scannedProducts.value.every(val => val === scannedProducts.value[0])) return true
        alert('Les produits scannés ne sont pas tous identiques, veuillez recommencer depuis le début')
        scannedProducts.value = []
        return false
    }
    function next() {
        if (product.value === '') return
        checkResult.value['class'] = ''
        checkResult.value.text = ''
        checkResult.value.state = true
        // On vérifie que la valeur product.value correspond à la valeur de props.of.data.productRef
        if (product.value === props.of.data.productRef) {
            scannedProducts.value.push(product.value)
            nbProduit.value = scannedProducts.value.length
            emits('changeProducts', nbProduit.value)
            product.value = ''
            return
        }
        checkResult.value['class'] = 'bg-danger'
        checkResult.value.text = `Le dernier produit scanné ne correspond pas à celui attendu ${props.of.data.productRef}, veuillez recommencer`
        checkResult.value.state = false
        product.value = ''
    }
    const newLabel = ref({})
    const zplHref = ref('')
    const imageUrl = ref('')
    function validate() {
        if (check()) {
            //Avant de passer à l'impression il faut créer l'étiquette avec l'ensemble des données
            const dataTosend = {
                labelKind: props.modeleEtiquette.labelKind,
                labelName: props.modeleEtiquette.labelName,
                templateFamily: props.modeleEtiquette.templateFamily,
                manufacturer: props.modeleEtiquette.manufacturer,
                customerAddressName: props.modeleEtiquette.customerAddressName,
                operator: props.operateur.matricule,
                batchnumber: props.of.data.ofnumber,
                productDescription: props.of.data.productDescription,
                productReference: props.of.data.productRef,
                productIndice: props.of.data.productIndice,
                quantity: nbProduit.value
            }
            const response = api('/api/label-cartons', 'post', dataTosend)
            // et récupérer le code ZPL
            response.then(data => {
                newLabel.value = data
                const file = new Blob([data.zpl], {type: 'text/plain'})
                zplHref.value = URL.createObjectURL(file)
                // http://api.labelary.com/v1/printers/{dpmm}/labels/{width}x{height}/{index}/{zpl}
                const dpmm = '8dpmm'
                const width = props.modeleEtiquette.width
                const height = props.modeleEtiquette.height
                const index = '0'
                imageUrl.value = `http://api.labelary.com/v1/printers/${dpmm}/labels/${width}x${height}/${index}/${data.zpl}`
                emits('nextStep', {
                    label: newLabel.value,
                    zpl: data.zpl,
                    imageUrl: imageUrl.value
                })
            })
            // avant de passer à l'étape suivante
        } else alert('Veuillez scanner au moins un produit')
    }
    function reset() {
        scannedProducts.value = []
        nbProduit.value = 0
        emits('changeProducts', nbProduit.value)
        product.value = ''
    }
    function removeLast() {
        scannedProducts.value.pop()
        nbProduit.value = scannedProducts.value.length
        emits('changeProducts', nbProduit.value)
    }
</script>

<template>
    <div>
        <div class="step-title">
            Scan Produits
        </div>
        <div class="align-items-stretch align-self-stretch d-flex flex-row justify-content-between" :class="checkResult['class']">
            <input id="product" ref="inputProduitRef" v-model="product" class="form-control m-2" type="text"/>
            <button class="btn btn-success m-2" @click="next">
                <Fa :brand="false" icon="plus"/>
            </button>
        </div>
        <div v-if="!checkResult.state" class="bg-danger text-center text-white">
            {{ checkResult.text }}
        </div>
        <div class="align-items-stretch align-self-stretch d-flex flex-row justify-content-between mt-3">
            <button class="btn btn-warning d-inline-block m-2" title="Recommencer" @click="reset">
                <Fa :brand="false" icon="backward-step"/> Recommencer les scans
            </button>
            <button class="btn btn-warning d-inline-block m-2" @click="removeLast">
                <Fa :brand="false" icon="rotate-left"/>
            </button>
            <button class="btn btn-success d-inline-block m-2" @click="validate">
                <Fa :brand="false" icon="chevron-right"/>
            </button>
        </div>
    </div>
</template>
