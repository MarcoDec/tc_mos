<script lang="ts" setup>
    import type {Actions, Getters} from '../../../../store/purchase/component/families'
    import {useNamespacedActions, useNamespacedGetters} from 'vuex-composition-helpers'
    import type {FormField} from '../../../../types/bootstrap-5'
    import {onMounted} from 'vue'

    const families = useNamespacedGetters<Getters>('families', ['tree']).tree
    const fields: FormField[] = [
        {label: 'Parent', name: 'parent', type: 'select'},
        {label: 'Code', name: 'code', type: 'text'},
        {label: 'Nom', name: 'name', type: 'text'},
        {label: 'Copperable', name: 'copperable', type: 'switch'},
        {label: 'Customs Code', name: 'customsCode', type: 'text'},
        {label: 'file', name: 'file', type: 'file'}
    ]
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
    <AppTreeRow :fields="fields" :item="families"/>
</template>
