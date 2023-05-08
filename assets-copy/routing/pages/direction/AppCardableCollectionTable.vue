<script lang="ts" setup>
    import type {FormField, ItemField} from '../../../types/bootstrap-5'
    import {ref} from 'vue'
    const roleuser = ref<string>('reader')
    const fields: FormField[] = [
        {ajoutVisible: true, label: 'Nom', min: true, name: 'name', trie: true, type: 'text', updateVisible: true},
        {ajoutVisible: true, label: 'Adresse', min: false, name: 'adresse', trie: true, type: 'text', updateVisible: true},
        {ajoutVisible: true, label: 'Complément d\'adresse', min: false, name: 'complement', trie: true, type: 'text', updateVisible: true},
        {ajoutVisible: true, label: 'Ville', min: true, name: 'ville', trie: true, type: 'text', updateVisible: true},
        {ajoutVisible: false, label: 'Pays', min: true, name: 'pays', options: [{text: 'aaaaa', value: 'aaaaa'}, {text: 'bbbb', value: 'bbbb'}], trie: true, type: 'select', updateVisible: true}
    ]
    const items: ItemField[] = [
        {adresse: 'Bd de l\'oise', ajout: false, complement: 'RUE DES ARTISANS', deletable: true, name: '3M FRANCE', pays: 'France', update: true, ville: 'CERGY PONTOISE'},
        {adresse: 'bbbbb', ajout: false, complement: 'RUE ', deletable: true, name: '3M FRANCE', pays: 'France', update: true, ville: 'CERGY PONTOISE'}

    ]
    const formData = ref<ItemField>({
        adresse: null, complement: null, name: null, pays: null, ville: null
    })

    const updated = ref(false)

    const form = ref(false)
    function ajoute(): void {
        form.value = true
    }
    function annule(): void {
        form.value = false
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
    function update(item: ItemField): void {
        updated.value = true
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
    <div class="row">
        <div class="col-11">
            <h1>
                <Fa icon="city"/>
                <span> Société</span>
            </h1>
        </div>
        <div class="col">
            <button class="btn btn-icon btn-success" @click="ajoute">
                <Fa icon="plus"/> Ajouter
            </button>
        </div>
    </div>
    <div class="row">
        <div class="col">
            <AppCollectionTable :fields="fields" :items="items" :min="form" :pag="true" :user="roleuser" @update="update"/>
        </div>
        <div v-if="form || updated " class="col">
            <AppCard class="bg-blue col">
                <div class="row">
                    <button id="btnRetour" class="btn btn-danger btn-icon btn-sm col-1" @click="annule">
                        <Fa icon="angle-double-left"/>
                    </button>
                    <h4 v-if="form" class="col">
                        <Fa icon="plus"/> Ajout
                    </h4>
                    <h4 v-else class="col">
                        <Fa icon="pencil-alt"/>  Modification
                    </h4>
                </div>
                <br/>
                <AppForm :fields="fields" :values="formData">
                    <template v-if="form" #buttons>
                        <AppBtn class="btnleft" variant="success" size="sm">
                            <Fa icon="plus"/> Ajouter
                        </AppBtn>
                    </template>
                    <template v-else #buttons>
                        <AppBtn class="btnleft" variant="success" size="sm">
                            <Fa icon="pencil-alt"/> Modifier
                        </AppBtn>
                    </template>
                </AppForm>
            </AppCard>
        </div>
    </div>
</template>

<style scoped>
.btnleft{
    float: right;
}
</style>
