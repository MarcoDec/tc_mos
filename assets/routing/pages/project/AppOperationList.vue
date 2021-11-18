<script lang="ts" setup>
    import type {FormField, ItemField} from '../../../types/bootstrap-5'

    const fields: FormField[] = [
        {label: 'Code', name: 'code', type: 'text'},
        {label: 'Nom', name: 'name', options: [{text: 'aaaaa', value: 'aaaaa'}, {text: 'bbbb', value: 'bbbb'}], type: 'select'},
        {label: 'Type', name: 'type', type: 'number'},
        {label: 'Auto', name: 'auto', type: 'grpbutton'},
        {label: 'Limite', name: 'limite', type: 'text'},
        {label: 'cadence', name: 'cadence', type: 'number'},
        {label: 'Prix', name: 'prix', type: 'number'},
        {label: 'Temps(en ms)', name: 'Temps', type: 'date'}
    ]
    const addRaw: FormField[] = [
        {label: 'Code', name: 'code', type: 'text'},
        {label: 'Nom', name: 'name', type: 'text'},
        {label: 'Type', name: 'type', type: 'number'},
        {label: 'Auto', name: 'auto', type: 'switch'},
        {label: 'Limite', name: 'limite', type: 'text'},
        {label: 'cadence', name: 'cadence', type: 'number'},
        {label: 'Prix', name: 'prix', type: 'number'},
        {label: 'Temps(en ms)', name: 'Temps', type: 'date'}
    ]
    const items: ItemField[] = [
        {cadence: '100', code: 'AS 01', limite: 'Mise En Bornier', name: 'Mise en bornnier', prix: '', temps: '', type: ''},
        {cadence: '100', code: 'AS 02', limite: 'Mise En Bornier', name: 'Mise en bornnier', prix: '', temps: '', type: ''}

    ]
</script>

<template>
    <h1>
        <Fa icon="atom"/>
        <span id="nameInterface"> Opération</span>
    </h1>
    <AppCollectionTable :fields="fields" :add-raw="addRaw" :items="items"/>
    <nav id="pagination" aria-label="Page navigation example">
        <ul class="pagination">
            <li class="page-item">
                <a class="page-link" href="#">Début</a>
            </li>
            <li class="page-item">
                <a class="page-link" href="#">Préc.</a>
            </li>
            <li class="page-item">
                <a class="page-link" href="#">1</a>
            </li>
            <li class="page-item">
                <a class="page-link" href="#">2</a>
            </li>
            <li class="page-item">
                <a class="page-link" href="#">3</a>
            </li>
            <li class="page-item">
                <a class="page-link" href="#">Suiv.</a>
            </li>
            <li class="page-item">
                <a class="page-link" href="#">Fin</a>
            </li>
        </ul>
    </nav>
</template>

<style scoped>
#pagination{
    float: right;
}
</style>
