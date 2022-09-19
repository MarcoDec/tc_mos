<script setup>
    import {computed, onMounted, onUnmounted, ref, watchPostEffect} from 'vue'
    import AppCurrency from '../../../components/management/AppCurrency'
    import AppCurrencySearch from '../../../components/management/AppCurrencySearch'
    import useCurrencies from '../../../stores/management/currencies'

    const el = ref()
    const fields = [
        {
            field: {label: 'Actif', name: 'active', type: 'boolean'},
            id: 'currencies-search-active'
        },
        {
            field: {label: 'Code', name: 'code'},
            id: 'currencies-search-code'
        },
        {
            field: {label: 'Nom', name: 'name'},
            id: 'currencies-search-name'
        }
    ]
    const height = ref(0)
    const heightPx = computed(() => `${height.value}px`)
    const store = useCurrencies()

    function reset() {
        store.resetFilters()
    }

    function resize() {
        if (el.value)
            height.value = window.top.innerHeight - el.value.getBoundingClientRect().top - 5
    }

    onMounted(async () => {
        await store.fetch()
        window.addEventListener('resize', resize)
        reset()
    })

    onUnmounted(() => {
        window.removeEventListener('resize', resize)
    })

    watchPostEffect(resize)
</script>

<template>
    <div ref="el" class="currencies overflow-hidden">
        <div class="row">
            <div class="col-2">
                <h1>
                    <Fa icon="euro-sign"/>
                    Devises
                </h1>
            </div>
            <div class="col">
                <div class="d-flex justify-content-between">
                    <AppBtn icon="times" title="Annuler" type="reset" variant="danger" @click="reset"/>
                    <AppCurrencySearch
                        v-for="{id, field} in fields"
                        :id="id"
                        :key="field.name"
                        :field="field"
                        :store="store"/>
                </div>
            </div>
        </div>
        <hr/>
        <div class="h-100 overflow-auto">
            <div v-for="(currencies, i) in store.split" :key="i" class="row">
                <AppCurrency v-for="currency in currencies" :key="currency.code" :currency="currency" :store="store"/>
            </div>
        </div>
    </div>
</template>

<style scoped>
    .currencies {
        height: v-bind('heightPx');
        max-height: v-bind('heightPx');
        min-height: v-bind('heightPx');
    }
</style>
