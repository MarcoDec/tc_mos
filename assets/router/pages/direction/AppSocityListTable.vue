<script setup>
    import {computed, ref} from 'vue'
    import {useSocietyListStore} from '../../../stores/direction/societyList'

    defineProps({
        fields: {default: () => [], type: Array},
        icon: {required: true, type: String},
        title: {required: true, type: String},
        modelValue:{}
    })
    const roleuser = ref('reader')

    const formData = ref({
        adresse: null, complement: null, name: null, pays: null, ville: null
    })

    const storeSocietyList = useSocietyListStore()
    storeSocietyList.fetch()
    
    const updated = ref(false)
    const AddForm = ref(false)
    let itemId = ''
    function ajoute(){
        AddForm.value = true
        updated.value = false
        const itemsNull = {
            adresse: null,
            complement: null,
            name: null,
            pays: null,
            ville: null
        }
        formData.value = itemsNull
    }
    function ajoutSociety(){
        const form = document.getElementById('addSociety')
        const formData= new FormData(form)
        const itemsAddData = {
            address:{
                address: formData.get('adresse'),
                address2: formData.get('complement'),
                country: formData.get('pays'),
                city: formData.get('ville')
            },
            name: formData.get('name')
        }
        storeSocietyList.addSociety(itemsAddData)
        // window.location.reload();
    }
    function annule(){
        AddForm.value = false
        updated.value = false
        const itemsNull = {
            adresse: null,
            complement: null,
            name: null,
            pays: null,
            ville: null
        }
        formData.value = itemsNull
    }
    function update(item) {
        updated.value = true
        AddForm.value = true
        itemId = Number(item['@id'].match(/\d+/)[0])
        const itemsData = {
            adresse: item.adresse,
            complement: item.complement,
            name: item.name,
            pays: item.pays,
            ville: item.ville
        }
        formData.value = itemsData
    }
    function updateSociety(){
        console.log('itemId',itemId);
        const form = document.getElementById('updateSociety')
        const formData= new FormData(form)
        const itemsUpdateData = {
            address:{
                address: formData.get('adresse'),
                address2: formData.get('complement'),
                country: formData.get('pays'),
                city: formData.get('ville')
            },
            name: formData.get('name')
        }
        console.log('itemsUpdateData',itemsUpdateData);
    }
    function deleted(id){
        storeSocietyList.delated(id)
    }
    function getPage(nPage){
       console.log('nPage', nPage);
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
                :create="true"
                :currentPage="storeSocietyList.currentPage"
                :fields="fields"
                :firstPage="storeSocietyList.firstPage"
                :items="storeSocietyList.societies"
                :lastPage="storeSocietyList.lastPage"
                :min="AddForm"
                :nextPage="storeSocietyList.nextPage"
                :pag="true"
                :previousPage="storeSocietyList.previousPage"
                :user="roleuser"
                form="formSocietyCardableTable"
                @update="update"
                @deleted="deleted"
                @getPage="getPage"/>
        </AppCol>
        <AppCol v-if="AddForm && !updated" class="col-7">
            <AppCard class="bg-blue col" title="">
                <AppRow>
                    <button id="btnRetour" class="btn btn-danger btn-icon btn-sm col-1" @click="annule">
                        <Fa icon="angle-double-left"/>
                    </button>
                    <h4 class="col">
                        <Fa icon="plus"/> Ajout
                    </h4>
                </AppRow>
                <br/>
                <AppFormCardable id="addSociety" :fields="fields" :model-value="formData" label-cols/>
                <AppCol class="btnright">
                    <AppBtn class="btnleft" label="Ajout" variant="success" size="sm" @click="ajoutSociety()">
                        <Fa icon="plus"/> Ajouter
                    </AppBtn>
                </AppCol>
            </AppCard>
        </AppCol>
        <AppCol v-else-if="AddForm && updated" class="col-7">
            <AppCard class="bg-blue col" title="">
                <AppRow>
                    <button id="btnRetour" class="btn btn-danger btn-icon btn-sm col-1" @click="annule">
                        <Fa icon="angle-double-left"/>
                    </button>
                    <h4 class="col">
                        <Fa icon="pencil-alt"/> Modification
                    </h4>
                </AppRow>
                <br/>
                <AppFormCardable id="updateSociety" :fields="fields" :model-value="formData"/>
                <AppCol class="btnright">
                    <AppBtn class="btnleft" label="retour" variant="success" size="sm" @click="updateSociety">
                        <Fa icon="pencil-alt"/> Modifier
                    </AppBtn>
                </AppCol>
            </AppCard>
        </AppCol>
    </AppRow>
</template>

<style scoped>
.btnleft{
    float: right;
}
</style>
