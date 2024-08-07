<script setup>
    import AppManufacturingTableItem from './AppManufacturingTableItem.vue'
    import {defineProps, ref} from 'vue'
    import api from "../../../api"
    import useUser from '../../../stores/security'

    const user = useUser()
    const emits = defineEmits(['oFsConfirmed', 'newOfsCreated'])
    const props = defineProps({
        fields: {required: true, type: Array},
        form: {required: true, type: String},
        id: {required: true, type: String},
        items: {required: true, type: Array},
        title: {required: true, type: String}
    })
    const localItems = ref([])
    localItems.value = props.items
    const lengthTable = props.fields.length - 1

    function confirmOFs() {
        const promises = []
        localSelectedItems.value.forEach(item => {
            // /api/manufacturing-orders/2/promote/manufacturing_order/to/accept
            const apiRequest = api(`/api/manufacturing-orders/${item.manufacturingOrderId}/promote/manufacturing_order/to/accept`, 'PATCH')
            promises.push(apiRequest)
        })
        Promise.all(promises).then(() => {
            emits('oFsConfirmed')
        })
    }
    async function createNewOFs() {
        //console.log('createNewOFs', localSelectedItems.value, props.items)
        const promises = []
        //On récupère la date du jour et on la met au format texte Ymd
        // const today = new Date()
        // const year = today.getFullYear()
        // const month = (today.getMonth() + 1).toString().padStart(2, '0')
        // const day = (today.getDate()).toString().padStart(2, '0')
        // const ref = `${year}${month}${day}`
        // console.log('selectedItems', localSelectedItems.value)
        await localSelectedItems.value.forEach(async item => {
            //const product = await api(item.productIri, 'GET')
            //si item.siteDeProduction ne contient pas la chaine texte '/api/compagnies' alors on affiche un message d'erreur
            if (typeof item.siteDeProduction === 'undefined' || !item.siteDeProduction.includes('/api/companies')) {
                console.error('Erreur lors de la création de l\'OF, le site de production n\'est pas valide', item)
                window.alert('Erreur lors de la création de l\'OF, le site de production n\'est pas valide')
                return
            }
            const data = {
                company: user.company,
                manufacturingDate: item.date,
                manufacturingCompany: item.siteDeProduction,
                product: item.productIri,
                quantityRequested: {
                    value: item.quantity,
                    code: 'U'
                }
                // ref: ref => doit être autogénéré coté serveur
            }
            const apiRequest = api('/api/manufacturing-orders', 'POST', data)
            promises.push(apiRequest)
        })
        Promise.all(promises).then(() => {
            console.log('newOfsCreated')
            emits('newOfsCreated')
        })
    }

    const localSelectedItems = ref([])
    function addSelectedItems(item) {
        //On vérifie si l'item n'est pas déjà dans la liste
        const index = localSelectedItems.value.findIndex(i => i.id === item.id)
        if (index !== -1) return
        localSelectedItems.value.push(item)
    }
    function removeSelectedItems(item) {
        const index = localSelectedItems.value.findIndex(i => i.id === item.id)
        if (index === -1) return
        localSelectedItems.value.splice(index, 1)
    }
    function onModelValueUpdated(item, newItem) {
        const index = localItems.value.findIndex(i => i.id === item.id)
        if (index === -1) return
        localItems.value[index] = newItem
        //On met à jour les items sélectionnés. Si l'item a sa propriété confirmerOF à true, on l'ajoute sinon on le retire
        if (newItem.confirmedOF || newItem.lancerOF) addSelectedItems(newItem)
        else if (!newItem.confirmedOF || !newItem.lancerOF) removeSelectedItems(newItem)
    }
</script>

<template>
    <tbody>
        <AppManufacturingTableItem
            v-for="item in localItems"
            :id="id"
            :key="item.id"
            :item="item"
            :fields="fields"
            :title="title"
            :form="form"
            @update:model-value="(newItem) => onModelValueUpdated(item, newItem)"/>
        <tr v-if="title === 'collapse new Ofs'">
            <td :colspan="lengthTable" class="bg-white text-center text-white"/>
            <td>
                <AppBtn type="submit" variant="success" label="" @click="createNewOFs">
                    Générer OFs
                </AppBtn>
            </td>
        </tr>
        <tr v-else-if="title === 'collapse ofs ToConfirm'">
            <td :colspan="lengthTable" class="bg-white text-center text-white"/>
            <td>
                <AppBtn type="submit" variant="success" label="" @click="confirmOFs">
                    Confirmer OFs
                </AppBtn>
            </td>
        </tr>
    </tbody>
</template>
