
<script setup>
    import {computed, ref} from 'vue'
    import {useSocietyListStore} from '../../../../../stores/societies/societyList'

    defineProps({
        icon: {required: true, type: String},
        title: {required: true, type: String}
    })

    const roleuser = ref('reader')
    let violations = []
    const updated = ref(false)
    const AddForm = ref(false)
    let itemId = ''
    const isPopupVisible = ref(false)
    const sortable = ref(false)
    const filter = ref(false)
    let trierAlpha = {}
    let filterBy = {}

    const storeSocietyList = useSocietyListStore()
    await storeSocietyList.fetch()
    await storeSocietyList.countryOption()
    const itemsTable = computed(() => storeSocietyList.itemsSocieties.reduce((acc, curr) => acc.concat(curr), []))
    const listCountry = computed(() => storeSocietyList.countriesOption)

    const formData = ref({
        adresse: null, complement: null, name: null, pays: null, ville: null
    })

    const fieldsForm = [
        {label: 'Nom*', name: 'name', type: 'text'},
        {label: 'Adresse*', name: 'address', type: 'text'},
        {label: 'Complément d\'adresse*', name: 'address2', type: 'text'},
        {label: 'Ville*', name: 'city', type: 'text'},
        {label: 'Pays*',
         name: 'country',
         options: {
             label: value =>
                 listCountry.value.find(option => option.type === value)?.text ?? null,
             options: listCountry.value
         },
         type: 'select'},
        {label: 'Zip Code*', name: 'zipCode', type: 'text'},
        {label: 'Phone Number*', name: 'phoneNumber', type: 'text'},
        {label: 'Email*', name: 'email', type: 'text'}
    ]

    const tabFields = [
        {label: 'Nom', min: true, name: 'name', trie: true, type: 'text'},
        {label: 'Adresse', min: false, name: 'address', trie: true, type: 'text'},
        {label: 'Complément d\'adresse', min: false, name: 'address2', trie: true, type: 'text'},
        {label: 'Ville', min: true, name: 'city', trie: true, type: 'text'},
        {label: 'Pays',
         min: true,
         name: 'country',
         options: {
             label: value =>
                 listCountry.value.find(option => option.type === value)?.text ?? null,
             options: listCountry.value
         },
         trie: true,
         type: 'select'}
    ]
    function ajoute(){
        AddForm.value = true
        updated.value = false
        const itemsNull = {
            address: null,
            address2: null,
            city: null,
            country: null,
            email: null,
            name: null,
            phoneNumber: null,
            zipCode: null
        }
        formData.value = itemsNull
    }
    async function ajoutSociety(){
        const form = document.getElementById('addSociety')
        const formData1 = new FormData(form)
        const itemsAddData = {
            address: {
                address: formData1.get('address'),
                address2: formData1.get('address2'),
                city: formData1.get('city'),
                country: formData1.get('country'),
                email: formData1.get('email'),
                phoneNumber: formData1.get('phoneNumber'),
                zipCode: formData1.get('zipCode')
            },
            name: formData1.get('name')
        }
        await storeSocietyList.addSociety(itemsAddData)
        AddForm.value = false
        updated.value = false
    }
    function annule(){
        AddForm.value = false
        updated.value = false
        const itemsNull = {
            address: null,
            address2: null,
            city: null,
            country: null,
            email: null,
            name: null,
            phoneNumber: null,
            zipCode: null
        }
        formData.value = itemsNull
        isPopupVisible.value = false
    }
    function update(item) {
        updated.value = true
        AddForm.value = true
        itemId = Number(item['@id'].match(/\d+/)[0])
        const itemsData = {
            address: item.address,
            address2: item.address2,
            city: item.city,
            country: item.country,
            email: item.email,
            name: item.name,
            phoneNumber: item.phoneNumber,
            zipCode: item.zipCode
        }
        formData.value = itemsData
    }
    async function updateSociety(){
        try {
            const form = document.getElementById('updateSociety')
            const formData2 = new FormData(form)
            const itemsUpdateData = {
                address: {
                    address: formData2.get('address'),
                    address2: formData2.get('address2'),
                    city: formData2.get('city'),
                    country: formData2.get('country'),
                    email: formData2.get('email'),
                    phoneNumber: formData2.get('phoneNumber'),
                    zipCode: formData2.get('zipCode')
                },
                name: formData2.get('name')
            }
            const payload = {
                filter,
                filterBy,
                id: itemId,
                itemsUpdateData,
                sortable,
                trierAlpha
            }
            await storeSocietyList.updateSociety(payload)
            AddForm.value = false
            updated.value = false
            isPopupVisible.value = false
        } catch (error) {
            violations = error
            isPopupVisible.value = true
        }
    }
    async function deleted(id){
        await storeSocietyList.delated(id)
    }
    async function getPage(nPage){
        await storeSocietyList.paginationSortableOrFilterItems({filter, filterBy, nPage, sortable, trierAlpha})
    }
    async function trierAlphabet(payload) {
        await storeSocietyList.sortableItems(payload, filterBy, filter)
        sortable.value = true
        trierAlpha = computed(() => payload)
    }
    async function search(inputValues) {
        const payload = {
            address: {
                address: inputValues.address ?? '',
                address2: inputValues.address2 ?? '',
                city: inputValues.city ?? '',
                country: inputValues.country ?? ''
            },
            name: inputValues.name ?? ''
        }
        await storeSocietyList.filterBy(payload)
        filter.value = true
        filterBy = computed(() => payload)
    }
    async function cancelSearch() {
        filter.value = false
        await storeSocietyList.fetch()
    }
</script>

<template>
    <div class="gui-bottom">
        <AppCol class="d-flex justify-content-between mb-2">
            <h1>
                <Fa :icon="icon"/>
                {{ title }}
            </h1>
            <AppBtn variant="success" label="Ajout" @click="ajoute">
                <Fa icon="plus"/>
                Ajouter
            </AppBtn>
        </AppCol>
        <AppRow>
            <AppCol>
                <AppCardableTable
                    :current-page="storeSocietyList.currentPage"
                    :fields="tabFields"
                    :first-page="storeSocietyList.firstPage"
                    :items="itemsTable"
                    :last-page="storeSocietyList.lastPage"
                    :min="AddForm"
                    :next-page="storeSocietyList.nextPage"
                    :pag="storeSocietyList.pagination"
                    :previous-page="storeSocietyList.previousPage"
                    :user="roleuser"
                    form="formSocietyCardableTable"
                    @update="update"
                    @deleted="deleted"
                    @get-page="getPage"
                    @trier-alphabet="trierAlphabet"
                    @search="search"
                    @cancel-search="cancelSearch"/>
            </AppCol>
            <AppCol v-if="AddForm && !updated" class="col-7">
                <AppCard class="bg-blue col" title="">
                    <AppRow>
                        <button id="btnRetour1" class="btn btn-danger btn-icon btn-sm col-1" @click="annule">
                            <Fa icon="angle-double-left"/>
                        </button>
                        <h4 class="col">
                            <Fa icon="plus"/> Ajout
                        </h4>
                    </AppRow>
                    <br/>
                    <AppFormCardable id="addSociety" :fields="fieldsForm" :model-value="formData" label-cols/>
                    <AppCol class="btnright">
                        <AppBtn class="btn-float-right" label="Ajout" variant="success" size="sm" @click="ajoutSociety">
                            <Fa icon="plus"/> Ajouter
                        </AppBtn>
                    </AppCol>
                </AppCard>
            </AppCol>
            <AppCol v-else-if="AddForm && updated" class="col-7">
                <AppCard class="bg-blue col" title="">
                    <AppRow>
                        <button id="btnRetour2" class="btn btn-danger btn-icon btn-sm col-1" @click="annule">
                            <Fa icon="angle-double-left"/>
                        </button>
                        <h4 class="col">
                            <Fa icon="pencil-alt"/> Modification
                        </h4>
                    </AppRow>
                    <br/>
                    <AppFormCardable id="updateSociety" :fields="fieldsForm" :model-value="formData" :violations="violations"/>
                    <div v-if="isPopupVisible" class="alert alert-danger" role="alert">
                        <div v-for="violation in violations" :key="violation">
                            <li>{{ violation.message }}</li>
                        </div>
                    </div>
                    <AppCol class="btnright">
                        <AppBtn class="btn-float-right" label="retour" variant="success" size="sm" @click="updateSociety">
                            <Fa icon="pencil-alt"/> Modifier
                        </AppBtn>
                    </AppCol>
                </AppCard>
            </AppCol>
        </AppRow>
    </div>
</template>

<style scoped>
    .btn-float-right{
        float: right;
    }
    .gui-bottom {
        overflow: hidden;
    }
</style>
