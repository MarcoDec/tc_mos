<script setup>
    import AppModal from '../../../../../modal/AppModal.vue'
    import AppFormJS from '../../../../../form/AppFormJS'
    import {computed, ref} from 'vue'
    import api from '../../../../../../api'
    import {Modal} from 'bootstrap'
    import Measure from './measure'

    const emits = defineEmits(['updated', 'closed'])
    const props = defineProps({
        customer: {default: () => ({}), required: true, type: Object},
        fields: {default: () => [], required: true, type: Array},
        formData: {default: () => ({}), required: true, type: Object},
        mode: {default: 'add', required: false, type: String},
        modalId: {required: true, type: String},
        order: {default: () => ({}), required: true, type: Object},
        optionsUnit: {default: () => ({}), required: true, type: Object},
        optionsCurrency: {default: () => ({}), required: true, type: Object},
        store: {default: () => ({}), required: true, type: Object},
        title: {default: 'Ajouter Item en Prévisionnel', required: false, type: String},
        btnLabel: {default: 'Ajouter', required: false, type: String}
    })
    const itemStore = props.store
    const measure = new Measure('U', 0.0)
    measure.initUnits()
    const formKey = ref(0)
    const localData = ref(props.formData)
    const modalRef = ref(null)
    const fieldsItem = computed(() => props.fields)
    const violations = ref([])
    const loaderShow = ref(false)

    async function updateValue(value, localData1) {
        if (typeof localData1 === 'undefined') {
            return
        }
        loaderShow.value = true
        // localData doit être mise à jour mais comme certaines actions et contrôles doivent être
        // effectués en fonction de la valeur initiale de localData, on crée une copie de localData
        // pour pouvoir comparer les valeurs initiales et les valeurs mises à jour
        const initialLocalData = {...localData1}
        Object.assign(localData1, value)
        if (value.product === null) localData1.product = null
        if (value.component === null) localData1.component = null
        // Si un produit a été sélectionné
        if (value.product && value.product !== initialLocalData.product) {
            localData1.component = null // On remet à zéro le composant
            // On récupère les données du produit
            await api(value.product, 'GET').then(async response => {
                const minDeliveryMeasure = new Measure(response.minDelivery.code, response.minDelivery.value, response.minDelivery.denominator, 'unit')
                await minDeliveryMeasure.init()
                await Measure.setQuantityToMinDelivery(localData1, minDeliveryMeasure)
                await Measure.getAndSetProductPrice(response, props.customer, props.order, localData1.requestedQuantity, localData1, formKey)
            })
            loaderShow.value = false
            return
        }
        if (value.component && value.component !== initialLocalData.component) {
            localData1.product = null
            await api(value.component, 'GET').then(async response => {
                await Measure.setQuantityToUnit(localData1, response)
                await Measure.getAndSetComponentPrice(response, props.customer, props.order, localData1.requestedQuantity, localData1, formKey)
            })
            loaderShow.value = false
            return
        }
        // Si la quantité demandée a été modifiée
        if (value.requestedQuantity && value.requestedQuantity !== initialLocalData.requestedQuantity) {
            await api(value.product, 'GET').then(async response => {
                await Measure.getAndSetProductPrice(response, props.customer, props.order, localData1.requestedQuantity, localData1, formKey)
            })
            loaderShow.value = false
            return
        }
        loaderShow.value = false
    }
    function checkData(data) {
        violations.value = []
        if (typeof data === 'undefined' || data === null) {
            violations.value.push({propertyPath: 'product', message: 'Veuillez remplir le formulaire'})
            return false
        }
        // on retire d'éventuelles violations précédentes liées à la propriété product
        violations.value = violations.value.filter(violation => violation.propertyPath !== 'product')
        if (data.product === null && data.component === null) {
            violations.value.push({propertyPath: 'product', message: 'Vous devez sélectionner un produit ou un composant'})
            return false
        }
        // on retire d'éventuelles violations précédentes liées à la propriété product
        violations.value = violations.value.filter(violation => violation.propertyPath !== 'product')
        if (typeof data.requestedQuantity === 'undefined' || data.requestedQuantity === null) {
            violations.value.push({propertyPath: 'requestedQuantity', message: 'Vous devez saisir une quantité'})
            return false
        }
        // on retire d'éventuelles violations précédentes liées à la propriété requestedQuantity
        violations.value = violations.value.filter(violation => violation.propertyPath !== 'requestedQuantity')
        if (data.requestedQuantity.code === null || data.requestedQuantity.value === null) {
            violations.value.push({propertyPath: 'requestedQuantity', message: 'Vous devez saisir une quantité'})
            return false
        }
        // on retire d'éventuelles violations précédentes liées à la propriété requestedQuantity
        violations.value = violations.value.filter(violation => violation.propertyPath !== 'requestedQuantity')
        if (data.requestedDate === null) {
            violations.value.push({propertyPath: 'requestedDate', message: 'Vous devez saisir une date'})
            return false
        }
        // on retire d'éventuelles violations précédentes liées à la propriété requestedDate
        violations.value = violations.value.filter(violation => violation.propertyPath !== 'requestedDate')
        if (data.price.code === null || data.price.value === null) {
            violations.value.push({propertyPath: 'price', message: 'Vous devez saisir un prix'})
            return false
        }
        // on retire d'éventuelles violations précédentes liées à la propriété price
        violations.value = violations.value.filter(violation => violation.propertyPath !== 'price')
        return true
    }

    async function addForecastItem() {
        if (!checkData(localData.value)) {
            throw new Error('Données invalides localData.value')
        }
        loaderShow.value = true
        //On ajoute le champ parentOrder
        localData.value.parentOrder = props.order['@id']
        //On ajoute le type d'item isForecast
        localData.value.isForecast = true
        //On remplace la valeur du code qui contient actuellement l'id de l'unité par le code de l'unité
        const requestedUnit = props.optionsUnit.find(unit => unit.value === localData.value.requestedQuantity.code)
        //Si c'est un produit on positionne la clé item avec la valeur de la clé produit, sinon on positionne la clé item avec la valeur de la clé component
        if (localData.value.product) localData.value.item = localData.value.product
        else localData.value.item = localData.value.component
        if (localData.value.confirmedQuantity) {
            //on retire la quantité confirmée
            delete localData.value.confirmedQuantity
        }
        //On remplace la valeur du code qui contient actuellement l'id de l'unité par le code de l'unité
        await api(localData.value.price.code, 'GET').then(currency => {
            localData.value.price.code = currency.code
        })
        // eslint-disable-next-line require-atomic-updates
        localData.value.requestedQuantity.code = requestedUnit.text
        //On ajoute l'item en base
        await itemStore.add(localData.value)
        //On ferme la modale
        if (modalRef.value) {
            const modalElement = modalRef.value.$el
            const bootstrapModal = Modal.getInstance(modalElement)
            loaderShow.value = false
            bootstrapModal.hide()
        }
        //On réinitalise les données locales
        // on désactive require-atomic-updates car on ne peut pas utiliser await dans une fonction non asynchrone
        // eslint-disable-next-line require-atomic-updates
        localData.value = {}
        //On rafraichit les données du tableau
        emits('updated')
    }
    async function onSubmit(data) {
        loaderShow.value = true
        if (props.mode === 'add') {
            await addForecastItem(data)
        }
        if (props.mode === 'edit') {
            await editForecastItem(data)
        }
        loaderShow.value = false
    }
    function editForecastItem(data) {
        console.log('Modification de l\'item en base', data)
    }
    function onModalClose() {
        emits('closed')
    }
</script>

<template>
    <AppModal
        :id="modalId"
        ref="modalRef"
        :modal-ref-name="modalId"
        :title="title"
        style="padding-bottom: 0"
        @closed="onModalClose">
        <div v-if="loaderShow" class="overlay-spinner d-flex justify-content-center align-items-center">
            <div class="spinner-border" role="status" aria-hidden="true"></div>
        </div>
        <AppFormJS
            id="formAddNewForecastOrderItem"
            :key="formKey"
            :model-value="localData"
            :fields="fieldsItem"
            :violations="violations"
            :submit-label="btnLabel"
            @update:model-value="value => updateValue(value, localData)"
            @submit="onSubmit"/>
    </AppModal>
</template>

<style scoped>
    .overlay-spinner {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.25);
        z-index: 1000;
        display: flex;
        justify-content: center;
        align-items: center;
    }
</style>
