<script setup>
    import {ref} from 'vue'
    import {useSocietyListStore} from '../../../stores/direction/societyList'

    defineProps({
        fields: {default: () => [], type: Array},
        icon: {required: true, type: String},
        title: {required: true, type: String}
    })
    const roleuser = ref('reader')

    const formData = ref({
        adresse: null, complement: null, name: null, pays: null, ville: null
    })

    const storeSocietyList = useSocietyListStore()
    storeSocietyList.fetchItems()

    const updated = ref(false)
    const AddForm = ref(false)
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
        const itemsData = {
            adresse: item.adresse,
            complement: item.complement,
            name: item.name,
            pays: item.pays,
            ville: item.ville
        }
        formData.value = itemsData
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
                :fields="fields"
                :items="storeSocietyList.items"
                :min="AddForm"
                :pag="true"
                :user="roleuser"
                form="formSocietyCardableTable"
                @update="update"/>
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
                <AppFormCadable id="addSociety" :fields="fields" :model-value="formData" label-cols/>
                <AppCol class="btnright">
                    <AppBtn class="btnleft" label="Ajout" variant="success" size="sm">
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
                        <Fa icon="pencil-alt"/>   Modification
                    </h4>
                </AppRow>
                <br/>
                <AppFormCadable id="updateSociety" :fields="fields" :model-value="formData"/>
                <AppCol class="btnright">
                    <AppBtn class="btnleft" label="retour" variant="success" size="sm">
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
