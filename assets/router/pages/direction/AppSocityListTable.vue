<script setup>
    import {computed, ref} from 'vue'
    import {useSocietyListStore} from '../../../stores/direction/societyList'


    const props = defineProps({
        fields: {default: () => [], type: Array},
        icon: {required: true, type: String},
        title: {required: true, type: String}
    })
    const roleuser = ref('reader')
    
   
    const storeSocietyList = useSocietyListStore()
    await storeSocietyList.fetch()
    await storeSocietyList.countryOption()
    const itemsTable = computed(()=>storeSocietyList.itemsSocieties.reduce((acc, curr) => acc.concat(curr), [])) 
    console.log('itemsTable',itemsTable);
    const listCountry = computed(()=>storeSocietyList.countriesOption)
    console.log('listCountryy', listCountry);

    const formData = ref({
        adresse: null, complement: null, name: null, pays: null, ville: null
    })


    const updated = ref(false)
    const AddForm = ref(false)
    let itemId = ''
    
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
                    options:listCountry.value
                 },
                 type: 'select'
                },
                {label: 'zipCode*', name: 'zipCode', type: 'text'},
                {label: 'phoneNumber*', name: 'phoneNumber', type: 'text'},
                {label: 'email*', name: 'email', type: 'text'}
            ]

    function ajoute(){
        AddForm.value = true
        updated.value = false
        const itemsNull = {
            address: null,
            address2: null,
            city: null,
            country: null,
            name: null,
            zipCode: null,
            phoneNumber: null,
            email: null
        }
        formData.value = itemsNull
    }
    function ajoutSociety(){
        const form = document.getElementById('addSociety')
        const formData1 = new FormData(form)
        const itemsAddData = {
            address: {
                address: formData1.get('address'),
                address2: formData1.get('address2'),
                city: formData1.get('city'),
                country: formData1.get('country'),
                zipCode: formData1.get('zipCode'),
                phoneNumber: formData1.get('phoneNumber'),
                email: formData1.get('email')
            },
            name: formData1.get('name')
        }
        //console.log('itemsAddData', itemsAddData)
        storeSocietyList.addSociety(itemsAddData)
    }
    function annule(){
        AddForm.value = false
        updated.value = false
        const itemsNull = {
            address: null,
            address2: null,
            city: null,
            country: null,
            name: null,
            zipCode: null,
            phoneNumber: null,
            email: null
        }
        formData.value = itemsNull
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
            name: item.name,
            zipCode: item.zipCode,
            phoneNumber: item.phoneNumber,
            email: item.email
        }
        formData.value = itemsData
    }
    function updateSociety(){
        //console.log('itemId', itemId)
        const form = document.getElementById('updateSociety')
        const formData2 = new FormData(form)
        const itemsUpdateData = {
            address: {
                address: formData2.get('address'),
                address2: formData2.get('address2'),
                city: formData2.get('city'),
                country: formData2.get('country'),
                zipCode: formData2.get('zipCode'),
                phoneNumber: formData2.get('phoneNumber'),
                email: formData2.get('email')
            },
            name: formData2.get('name')
        }
        //console.log('itemsUpdateData', itemsUpdateData)
        const payload = {
            id: itemId,
            itemsUpdateData
        }
        storeSocietyList.updateSociety(payload)
    }
    function deleted(id){
        storeSocietyList.delated(id)
    }
    function getPage(nPage){
        storeSocietyList.itemsPagination(nPage)
    }
</script>

<template>
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
                :fields="fields"
                :first-page="storeSocietyList.firstPage"
                :items="itemsTable"
                :last-page="storeSocietyList.lastPage"
                :min="AddForm"
                :next-page="storeSocietyList.nextPage"
                :pag="true"
                :previous-page="storeSocietyList.previousPage"
                :user="roleuser"
                form="formSocietyCardableTable"
                @update="update"
                @deleted="deleted"
                @get-page="getPage"/>
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
                <AppFormCardable id="updateSociety" :fields="fieldsForm" :model-value="formData"/>
                <AppCol class="btnright">
                    <AppBtn class="btn-float-right" label="retour" variant="success" size="sm" @click="updateSociety">
                        <Fa icon="pencil-alt"/> Modifier
                    </AppBtn>
                </AppCol>
            </AppCard>
        </AppCol>
    </AppRow>
</template>

<style scoped>
    .btn-float-right{
        float: right;
    }
</style>
