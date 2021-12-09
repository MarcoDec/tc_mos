<script lang="ts" setup>
    import {onMounted , ref} from 'vue'
    import {ActionTypes} from '../../../../store/purchase/component'
    import type {Actions} from '../../../../store/purchase/component'
    import {useNamespacedActions, useNamespacedGetters} from 'vuex-composition-helpers'
    import type {TreeItem} from '../../../../types/tree'

    const fields: FormField[] = [
        {label: 'Parent', name: 'parent', type: 'select'},
        {label: 'Code', name: 'code', type: 'text'},
        {label: 'Nom', name: 'name',  type: 'text'},
        {label: 'Copperable', name: 'copperable', type: 'switch'},
        {label: 'Customs Code', name: 'customsCode', type: 'text'},
        {label: 'file', name: 'file', type: 'file'}
    ]
    const formData = ref ({
        parent: null, code: null, name: null, copperable: false, customsCode: null, file:''
    })
    const addFamilies = useNamespacedActions<Actions>('component', [ActionTypes.ADD_FAMILIES])[ActionTypes.ADD_FAMILIES]

    onMounted(async () => {
        await useNamespacedActions<Actions>('component', [ActionTypes.LOAD_FAMILIES])[ActionTypes.LOAD_FAMILIES]()
    });
    const families = useNamespacedGetters('component', ['treeFamilies']).treeFamilies
    console.log('families', families);

    async function addFamily ():void {
        console.log('add',formData)
        await addFamilies(formData.value)
    }
    function selected (item : TreeItem):void{
        // formData.value= item
        console.log('formData',item);
        
        const items = {
            pathid: item['@id'],
            type: item ['@type'],
            children: item.children,
            code: item.code,
            copperable: item.copperable,
            customsCode: item.customsCode,
            filepath: item.filepath,
            id: item.id,
            name: item.name,
            parent: item.parent['@id']
        }
        console.log('items',items);
        formData.value= items
    }
</script>

<template>
    <AppRow>
        <h1 class="col">
            <Fa class="me-3" icon="layer-group"/>
            Familles
        </h1>
    </AppRow>
    <AppTreeRow :item="families" v-model:formData="formData" :fields="fields" label="code" @ajout="addFamily" @selected="selected"/>
</template>
