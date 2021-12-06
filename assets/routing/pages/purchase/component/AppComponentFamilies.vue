<script lang="ts" setup>
    import {onMounted} from 'vue'
    import {ActionTypes} from '../../../../store/purchase/component'
    import type {Actions} from '../../../../store/purchase/component'
    import {useNamespacedActions, useNamespacedGetters} from 'vuex-composition-helpers'
    import type {TreeItem} from '../../../../types/tree'

    const fields: FormField[] = [
        {label: 'Parent', name: 'parent', type: 'text'},
        {label: 'Code', name: 'code', type: 'text'},
        {label: 'Nom', name: 'name',  type: 'text'},
        {label: 'Copperable', name: 'copperable', type: 'switch'},
        {label: 'Customs Code', name: 'customsCode', type: 'text'},
        {label: 'file', name: 'file', type: 'file'}
    ]
    const formData = {
        parent: null, code: null, name: null, copperable: false, customsCode: null, file:''
    }
    // const addFamilies = await useNamespacedActions<Actions>('component', [ActionTypes.ADD_FAMILIES])[ActionTypes.ADD_FAMILIES]()

    onMounted(async () => {
        await useNamespacedActions<Actions>('component', [ActionTypes.LOAD_FAMILIES])[ActionTypes.LOAD_FAMILIES]()
    });
    const families = useNamespacedGetters('component', ['getListComponentFamiliesInfo']).getListComponentFamiliesInfo
    console.log('families', families);

    async function addFamily ():void {
        console.log('add',formData)
    }
   
</script>

<template>
    <AppRow>
        <h1 class="col">
            <Fa class="me-3" icon="layer-group"/>
            Familles
        </h1>
    </AppRow>
    <AppTreeRow :item="families" :fields="fields" label="code" @ajout="addFamily"/>
</template>
