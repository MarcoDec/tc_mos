<script setup>
    import AppCurrency from './AppCurrency.vue'
    import {CurrencyRepository} from '../../../store/modules'
    import {computed} from 'vue'
    import {useRepo} from '../../../composition'

    const fields = [
        {label: 'Actif', name: 'active', type: 'boolean'},
        {label: 'Code', name: 'code'},
        {label: 'Nom', name: 'name'}
    ]
    const repo = useRepo(CurrencyRepository)
    const split = computed(() => repo.split())
</script>

<template>
    <AppRow>
        <AppCol :cols="2">
            <h1>
                <Fa icon="euro-sign"/>
                Devises
            </h1>
        </AppCol>
        <AppCol>
            <AppForm id="currencies-search" :fields="fields" d-inline state-machine="currencies-search"/>
        </AppCol>
    </AppRow>
    <hr/>
    <AppRow v-for="(currencies, i) in split" :key="i">
        <AppCurrency v-for="currency in currencies" :key="currency.code" :currency="currency"/>
    </AppRow>
</template>
