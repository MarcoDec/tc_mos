<script lang="ts" setup>
    import type {FormField, ItemField} from '../../../types/bootstrap-5'
    import {ref} from 'vue'
    const roleuser = ref<string>('admin')
    const fields: FormField[] = [
        {ajoutVisible: true, label: 'Code', name: 'code', trie: true, type: 'text', updateVisible: true},
        {ajoutVisible: false, label: 'Nom', name: 'name', options: [{text: 'aaaaa', value: 'aaaaa'}, {text: 'bbbb', value: 'bbbb'}], trie: true, type: 'select', updateVisible: true},
        {ajoutVisible: true, label: 'Type', name: 'type', trie: true, type: 'number', updateVisible: true},
        {ajoutVisible: false, label: 'Auto', name: 'auto', trie: true, type: 'boolean', updateVisible: true},
        {ajoutVisible: true, label: 'Limite', name: 'limite', trie: false, type: 'text', updateVisible: true},
        {ajoutVisible: false, label: 'cadence', name: 'cadence', trie: true, type: 'number', updateVisible: false},
        {ajoutVisible: true, label: 'Prix', name: 'prix', trie: true, type: 'number', updateVisible: true},
        {ajoutVisible: true, label: 'Temps(en ms)', name: 'Temps', trie: false, type: 'date', updateVisible: false}
    ]

    const items: ItemField[] = [
        {ajout: false, cadence: 100, code: 'AS 01', deletable: true, limite: 'Mise En Bornier', name: 'Mise en bornnier', prix: null, temps: null, type: null},
        {ajout: true, cadence: 100, code: 'AS 02', deletable: false, limite: 'Mise En Bornier', name: 'Mise en bornnier', prix: null, temps: null, type: null}
    ]
</script>

<template>
    <h1>
        <Fa icon="atom"/>
        <span> Opération</span>
    </h1>
    <AppCollectionTable :fields="fields" :items="items" :pag="true" :user="roleuser"/>
</template>
