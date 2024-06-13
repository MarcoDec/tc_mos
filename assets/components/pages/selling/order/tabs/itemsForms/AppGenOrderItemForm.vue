<script setup>
    import AppModal from '../../../../../modal/AppModal.vue'
    import AppFormJS from '../../../../../form/AppFormJS'
    import {computed, ref} from 'vue'
    import api from '../../../../../../api'
    import {Modal} from 'bootstrap'
    import Measure from './measure'

    const emits = defineEmits(['updated', 'closed'])
    const props = defineProps({
        btnLabel: {default: 'Ajouter', required: false, type: String},
        customer: {default: () => ({}), required: true, type: Object},
        fields: {default: () => [], required: true, type: Array},
        formData: {default: () => ({}), required: true, type: Object},
        modalId: {required: true, type: String},
        mode: {default: 'add', required: false, type: String}, // ou edit
        optionsCurrency: {default: () => ({}), required: true, type: Object},
        optionsUnit: {default: () => ({}), required: true, type: Object},
        order: {default: () => ({}), required: true, type: Object},
        store: {default: () => ({}), required: true, type: Object},
        title: {default: 'Ajouter Item en Prévisionnel', required: false, type: String},
        variant: {default: 'fixed', required: true, type: String} //ou forecast
    })
    console.log('initialisation composant AppGenOrderItemForm', props.optionsCurrency)
    const itemStore = props.store
    const measure = new Measure('U', 0.0)
    measure.initUnits()
    const formKey = ref(0)
    const localData = ref(props.formData)
    const modalRef = ref(null)
    const fieldsItem = computed(() => props.fields)
    const violations = ref([])
    const loaderShow = ref(false)

    async function updateValue(value) {
        console.log('onUpdateValue', value, localData.value)
        if (typeof localData.value === 'undefined') {
            console.log('localData.value is empty => return')
            return
        }
        loaderShow.value = true
        // localData doit être mise à jour mais comme certaines actions et contrôles doivent être
        // effectués en fonction de la valeur initiale de localData, on crée une copie de localData
        // pour pouvoir comparer les valeurs initiales et les valeurs mises à jour
        const initialLocalData = {...localData.value}
        Object.assign(localData.value, value)
        console.log('value', value)
        console.log('localData.value après assign', localData.value)
        if (value.product === null) {
            console.log('value.product is null => set localData.value.product to null')
            localData.value.product = null
        }
        if (value.component === null) {
            console.log('value.component is null => set localData.value.component to null')
            localData.value.component = null
        }
        // Si un produit a été sélectionné
        if (value.product && value.product !== initialLocalData.product) {
            localData.value.component = null // On remet à zéro le composant
            // On récupère les données du produit
            await api(value.product, 'GET').then(async response => {
                const minDeliveryMeasure = new Measure(response.minDelivery.code, response.minDelivery.value, response.minDelivery.denominator, 'unit')
                await minDeliveryMeasure.init()
                await Measure.setQuantityToMinDelivery(minDeliveryMeasure)
                await Measure.getAndSetProductPrice(response, props.customer, props.order, localData.value.requestedQuantity, localData.value, formKey)
            })
            loaderShow.value = false
            return
        }
        if (value.component && value.component !== initialLocalData.component) {
            localData.value.product = null
            await api(value.component, 'GET').then(async response => {
                await Measure.setQuantityToUnit(localData.value, response)
                await Measure.getAndSetComponentPrice(response, props.customer, props.order, localData.value.requestedQuantity, localData.value, formKey)
            })
            loaderShow.value = false
            return
        }
        // Si la quantité demandée a été modifiée
        if (value.requestedQuantity && value.requestedQuantity !== initialLocalData.requestedQuantity) {
            await api(value.product, 'GET').then(async response => {
                await Measure.getAndSetProductPrice(response, props.customer, props.order, localData.value.requestedQuantity, localData.value, formKey)
            })
            loaderShow.value = false
            return
        }
        if (props.variant === 'fixed') {
            if (value.confirmedQuantity && value.confirmedQuantity !== initialLocalData.confirmedQuantity) {
                await api(value.product, 'GET').then(async response => {
                    await Measure.getAndSetProductPrice(response, props.customer, props.order, localData.value.confirmedQuantity, localData.value, formKey)
                })
                loaderShow.value = false
                return
            }
        }
        loaderShow.value = false
    }
    function checkData(data) {
        console.log('data', data)
        console.log('localData', localData.value)
        violations.value = []
        if (typeof data === 'undefined' || data === null) {
            console.log('form data is empty')
            violations.value.push({propertyPath: 'product', message: 'Veuillez remplir le formulaire'})
            return false
        }
        // on retire d'éventuelles violations précédentes liées à la propriété product
        violations.value = violations.value.filter(violation => violation.propertyPath !== 'product')
        if (data.product === null && data.component === null) {
            console.log('product and component are empty')
            violations.value.push({propertyPath: 'product', message: 'Vous devez sélectionner un produit ou un composant'})
            return false
        }
        // on retire d'éventuelles violations précédentes liées à la propriété product
        violations.value = violations.value.filter(violation => violation.propertyPath !== 'product')
        if (typeof data.requestedQuantity === 'undefined' || data.requestedQuantity === null) {
            console.log('requestedQuantity is empty')
            violations.value.push({propertyPath: 'requestedQuantity', message: 'Vous devez saisir une quantité'})
            return false
        }
        // on retire d'éventuelles violations précédentes liées à la propriété requestedQuantity
        violations.value = violations.value.filter(violation => violation.propertyPath !== 'requestedQuantity')
        if (data.requestedQuantity.code === null || data.requestedQuantity.value === null) {
            console.log('requestedQuantity.code or requestedQuantity.value is empty')
            violations.value.push({propertyPath: 'requestedQuantity', message: 'Vous devez saisir une quantité'})
            return false
        }
        // on retire d'éventuelles violations précédentes liées à la propriété requestedQuantity
        violations.value = violations.value.filter(violation => violation.propertyPath !== 'requestedQuantity')
        if (data.requestedDate === null) {
            console.log('requestedDate is empty')
            violations.value.push({propertyPath: 'requestedDate', message: 'Vous devez saisir une date'})
            return false
        }
        // on retire d'éventuelles violations précédentes liées à la propriété requestedDate
        violations.value = violations.value.filter(violation => violation.propertyPath !== 'requestedDate')
        if (data.price.code === null || data.price.value === null) {
            console.log('price.code or price.value is empty')
            violations.value.push({propertyPath: 'price', message: 'Vous devez saisir un prix'})
            return false
        }
        // on retire d'éventuelles violations précédentes liées à la propriété price
        violations.value = violations.value.filter(violation => violation.propertyPath !== 'price')
        return true
    }

    async function addItem() {
        console.log('addItem localData', localData.value)
        if (!checkData(localData.value)) {
            throw new Error('Données invalides localData.value')
        }
        loaderShow.value = true
        //On ajoute le champ parentOrder
        localData.value.parentOrder = props.order['@id']
        //On ajoute le type d'item isForecast
        localData.value.isForecast = props.variant === 'forecast'
        //On remplace la valeur du code qui contient actuellement l'id de l'unité par le code de l'unité
        const requestedUnit = props.optionsUnit.find(unit => unit.value === localData.value.requestedQuantity.code)
        console.log('requestedUnit', requestedUnit)
        console.log('localData.value.requestedQuantity', localData.value.requestedQuantity)
        // eslint-disable-next-line require-atomic-updates
        localData.value.requestedQuantity.code = requestedUnit.text
        if (props.variant === 'fixed') {
            const confirmedUnit = props.optionsUnit.find(unit => unit.value === localData.value.confirmedQuantity.code)
            console.log('confirmedUnit', confirmedUnit)
            console.log('localData.value.confirmedQuantity', localData.value.confirmedQuantity)
            // eslint-disable-next-line require-atomic-updates
            localData.value.confirmedQuantity.code = confirmedUnit.text
        } else {
            delete localData.value.confirmedQuantity
        }
        //Si c'est un produit on positionne la clé item avec la valeur de la clé produit, sinon on positionne la clé item avec la valeur de la clé component
        if (localData.value.product) localData.value.item = localData.value.product
        else localData.value.item = localData.value.component
        //On remplace la valeur du code qui contient actuellement l'id de l'unité par le code de l'unité
        await api(localData.value.price.code, 'GET').then(currency => {
            localData.value.price.code = currency.code
        })
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
        //localData.value = {}
        //On rafraichit les données du tableau
        emits('updated')
    }
    // TODO: à compléter
    async function editItem() {
        if (!checkData(localData.value)) {
            throw new Error('Données invalides localData.value')
        }
        loaderShow.value = true
        //On ajoute le champ parentOrder
        localData.value.parentOrder = props.order['@id']
        //On ajoute le type d'item isForecast
        localData.value.isForecast = props.variant === 'forecast'
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
    async function onSubmit() {
        console.log('onSubmit', localData.value)
        loaderShow.value = true
        if (props.mode === 'add') {
            await addItem(localData.value)
            loaderShow.value = false
            return
        }
        if (props.mode === 'edit') {
            await editItem(localData.value)
            loaderShow.value = false
            return
        }
        loaderShow.value = false
    }
    function onModalClose() {
        emits('closed')
    }
</script>

<template>
    <AppModal
        :id="modalId"
        ref="modalRef"
        class="padding-bottom-0"
        :modal-ref-name="modalId"
        :title="title"
        @closed="onModalClose">
        <div v-if="loaderShow" class="overlay-spinner d-flex justify-content-center align-items-center">
            <div class="spinner-border" role="status" aria-hidden="true"/>
        </div>
        <AppFormJS
            id="formAddNewForecastOrderItem"
            :key="formKey"
            :model-value="localData"
            :fields="fieldsItem"
            :violations="violations"
            :submit-label="btnLabel"
            @update:model-value="updateValue"
            @submit="onSubmit"/>
    </AppModal>
</template>

<style scoped>
    .overlay-spinner .spinner-border {
        color: #fff;
    }
    .padding-bottom-0 {
        padding-bottom: 0;
    }
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
