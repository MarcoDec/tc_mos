<script setup>
    import {ref,computed} from 'vue'
    import {useSocietyListStore} from '../../../stores/direction/societyList'

    defineProps({
        fields: {default: () => [], type: Array},
        icon: {required: true, type: String},
        title: {required: true, type: String}
    })
    const roleuser = ref('reader')
    const fieldsForm = [
                {label: 'Nom*', min: true, name: 'name', trie: true, type: 'text'},
                {label: 'Adresse*', min: false, name: 'address', trie: true, type: 'text'},
                {label: 'Complément d\'adresse*', min: false, name: 'address2', trie: true, type: 'text'},
                {label: 'Ville*', min: true, name: 'city', trie: true, type: 'text'},
                {label: 'Pays*', min: true, name: 'country', trie: true, type: 'text'}
            ]

    const formData = ref({
        adresse: null, complement: null, name: null, pays: null, ville: null
    })

    const storeSocietyList = useSocietyListStore()
    storeSocietyList.fetch()
    storeSocietyList.countryOption()

    const updated = ref(false)
    const AddForm = ref(false)
    let itemId = ''

    const list = computed(()=> storeSocietyList.societies.map((item)=> {
        const { address, address2, city, country} = item.address 
        let itemsTab = []
        const newObject = { 
            ...item,
            address: undefined, // Remove the original nested address object
            address: address ?? null,
            address2: address2 ?? null,
            city: city ?? null,
            country: country ?? null
        }  
        itemsTab.push(newObject)
        return itemsTab
    }) ) 
    const itemsTable = computed(()=>list.value.reduce((acc, curr) => acc.concat(curr), [])) 

    function ajoute(){
        AddForm.value = true
        updated.value = false
        const itemsNull = {
            address: null,
            address2: null,
            name: null,
            city: null,
            country: null
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
                country: formData1.get('country')
            },
            name: formData1.get('name')
        }
        console.log('itemsAddData',itemsAddData);
        storeSocietyList.addSociety(itemsAddData)
    }
    function annule(){
        AddForm.value = false
        updated.value = false
        const itemsNull = {
            address: null,
            address2: null,
            name: null,
            city: null,
            country: null
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
            name: item.name,
            city: item.city,
            country: item.country
        }
        formData.value = itemsData
    }
    function updateSociety(){
        console.log('itemId',itemId);
        const form = document.getElementById('updateSociety')
        const formData= new FormData(form)
        const itemsUpdateData = {
            address:{
                address: formData.get('address'),
                address2: formData.get('address2'),
                country: formData.get('country'),
                city: formData.get('city')
            },
            name: formData.get('name')
        }
        console.log('itemsUpdateData',itemsUpdateData);
        const payload ={
            id: itemId,
            itemsUpdateData: itemsUpdateData
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
                <!-- <div v-if="state.matches('error')" class="alert alert-danger" role="alert">
                    {{ state.context.error }}
                </div> -->
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
