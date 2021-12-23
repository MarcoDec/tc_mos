<script lang="ts" setup>
    /* eslint-disable @typescript-eslint/ban-ts-comment */
    import {onMounted, ref} from 'vue'
    import {useNamespacedActions, useNamespacedGetters} from 'vuex-composition-helpers'
    import {ActionTypes} from '../../../../store/purchase/component'
    import type {Actions} from '../../../../store/purchase/component'
    import type {FormField} from '../../../../types/bootstrap-5'
    import type {TreeItem} from '../../../../types/tree'

    const fields: FormField[] = [
        {label: 'Parent', name: 'parent', type: 'select'},
        {label: 'Code', name: 'code', type: 'text'},
        {label: 'Nom', name: 'name', type: 'text'},
        {label: 'Copperable', name: 'copperable', type: 'switch'},
        {label: 'Customs Code', name: 'customsCode', type: 'text'},
        {label: 'file', name: 'file', type: 'file'}
    ]
    const formData = ref({code: null, copperable: false, customsCode: null, file: '', name: null, parent: null})
    const addFamilies = useNamespacedActions<Actions>('component', [ActionTypes.ADD_FAMILIES])[ActionTypes.ADD_FAMILIES]

    onMounted(async () => {
        await useNamespacedActions<Actions>('component', [ActionTypes.LOAD_FAMILIES])[ActionTypes.LOAD_FAMILIES]()
    })
    const families = useNamespacedGetters('component', ['treeFamilies']).treeFamilies

    async function addFamily(): Promise<void> {
        // @ts-ignore
        await addFamilies(formData.value)
    }

    function selected(item: TreeItem): void {
        const items = {
            children: item.children,
            code: item.code,
            copperable: item.copperable,
            customsCode: item.customsCode,
            filepath: item.filepath,
            id: item.id,
            name: item.name,
            // @ts-ignore
            parent: item.parent['@id'],
            pathid: item['@id'],
            type: item['@type']
        }
        // @ts-ignore
        formData.value = items
    }
</script>

<template>
    <AppRow>
        <h1 class="col">
            <Fa class="me-3" icon="layer-group"/>
            Familles
        </h1>
    </AppRow>
    <AppTreeRow
        v-model:formData="formData" :fields="fields" :item="families" label="code" @ajout="addFamily"
        @selected="selected"/>
</template>
