<script setup>
    import {computed, onMounted, onUnmounted, ref, watchPostEffect} from 'vue'
    import AppCurrency from './AppCurrency.vue'
    import {CurrencyRepository} from '../../../store/modules'
    import {useRepo} from '../../../composition'

    const el = ref()
    const fields = [
        {label: 'Actif', name: 'active', type: 'boolean'},
        {label: 'Code', name: 'code'},
        {label: 'Nom', name: 'name'}
    ]
    const height = ref(0)
    const heightPx = computed(() => `${height.value}px`)
    const repo = useRepo(CurrencyRepository)
    const split = ref([])

    function filter({active, code, name}) {
        split.value = repo.filter(active, code, name)
    }

    function reset() {
        split.value = repo.split()
    }

    function resize() {
        if (el.value)
            height.value = window.top.innerHeight - el.value.getBoundingClientRect().top - 5
    }

    onMounted(() => {
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
        <AppRow>
            <AppCol :cols="2">
                <h1>
                    <Fa icon="euro-sign"/>
                    Devises
                </h1>
            </AppCol>
            <AppCol>
                <AppForm
                    id="currencies-search"
                    :fields="fields"
                    d-inline
                    state-machine="currencies-search"
                    @submit="filter">
                    <template #before>
                        <div>
                            <AppBtn icon="search" title="Rechercher" type="submit" variant="secondary"/>
                            <AppBtn icon="times" title="Annuler" type="reset" variant="danger" @click="reset"/>
                        </div>
                    </template>
                </AppForm>
            </AppCol>
        </AppRow>
        <hr/>
        <div class="h-100 overflow-auto">
            <AppRow v-for="(currencies, i) in split" :key="i">
                <AppCurrency v-for="currency in currencies" :key="currency.code" :currency="currency"/>
            </AppRow>
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
