<script lang="ts" setup>
    import type {Actions, Getters} from '../../../../../store/purchase/component/families'
    import {computed, onMounted} from 'vue'
    import {useNamespacedActions, useNamespacedGetters} from 'vuex-composition-helpers'
    import type {FormField} from '../../../../../types/bootstrap-5'

    const {options, tree} = useNamespacedGetters<Getters>('families', ['options', 'tree'])
    const fields = computed<FormField[]>(() => [
        {label: 'Parent', name: 'parent', options: options.value, type: 'select'},
        {label: 'Code', name: 'code', type: 'text'},
        {label: 'Nom', name: 'name', type: 'text'},
        {label: 'Copperable', name: 'copperable', type: 'boolean'},
        {label: 'Customs Code', name: 'customsCode', type: 'text'},
        {label: 'file', name: 'file', type: 'file'}
    ])
    const {load} = useNamespacedActions<Actions>('families', ['create', 'load'])

    onMounted(load)
</script>

<template>
    <AppRow>
        <h1 class="col">
            <Fa class="me-3" icon="layer-group"/>
            Familles
        </h1>
    </AppRow>
    <AppTreeRow id="component-families" :fields="fields" :item="tree"/>
</template>
