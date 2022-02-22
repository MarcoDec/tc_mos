<script lang="ts" setup>
    import type {Actions, Getters} from '../../../../../store/purchase/component/families'
    import {computed, onMounted, provide, ref} from 'vue'
    import {useNamespacedActions, useNamespacedGetters} from 'vuex-composition-helpers'
    import type {FormField} from '../../../../../types/bootstrap-5'

    const load = useNamespacedActions<Actions>('families', ['load']).load
    const {options} = useNamespacedGetters<Getters>('families', ['options'])
    const fields = computed<FormField[]>(() => [
        {label: 'Parent', name: 'parent', options: options.value, type: 'select'},
        {label: 'Code', name: 'code', type: 'text'},
        {label: 'Nom', name: 'name', type: 'text'},
        {label: 'Cuivre', name: 'copperable', type: 'boolean'},
        {label: 'Code douanier', name: 'customsCode', type: 'text'},
        {label: 'Icône', name: 'file', type: 'file'}
    ])
    const loaded = ref(false)

    provide('fields', fields)
    onMounted(async () => {
        await load()
        loaded.value = true
    })
</script>

<template>
    <AppRow>
        <h1 class="col">
            <Fa class="me-3" icon="layer-group"/>
            Familles
        </h1>
    </AppRow>
    <AppTreeRow v-if="loaded" id="component-families"/>
</template>
