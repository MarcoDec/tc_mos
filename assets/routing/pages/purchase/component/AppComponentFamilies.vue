<script lang="ts" setup>
    /* eslint-disable @typescript-eslint/no-unsafe-assignment,@typescript-eslint/ban-ts-comment */
    import {onMounted, ref} from 'vue'
    import {useNamespacedActions, useNamespacedGetters} from 'vuex-composition-helpers'
    import type {Actions} from '../../../../store/purchase/component/families'
    import type {FormField} from '../../../../types/bootstrap-5'
    import type {TreeItem} from '../../../../types/tree'

    const families = useNamespacedGetters('families', ['treeFamilies']).treeFamilies
    const fields: FormField[] = [
        {label: 'Parent', name: 'parent', type: 'select'},
        {label: 'Code', name: 'code', type: 'text'},
        {label: 'Nom', name: 'name', type: 'text'},
        {label: 'Copperable', name: 'copperable', type: 'switch'},
        {label: 'Customs Code', name: 'customsCode', type: 'text'},
        {label: 'file', name: 'file', type: 'file'}
    ]
    const formData = ref({code: null, copperable: false, customsCode: null, file: '', name: null, parent: null})
    const {create, load} = useNamespacedActions<Actions>('families', ['create', 'load'])

    async function addFamily(): Promise<void> {
        // @ts-ignore
        await create(formData.value)
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

    onMounted(load)
</script>

<template>
    <AppRow>
        <h1 class="col">
            <Fa class="me-3" icon="layer-group"/>
            Familles
        </h1>
    </AppRow>
    <AppTreeRow
        v-model:formData="formData"
        :fields="fields"
        :item="families"
        label="code"
        @ajout="addFamily"
        @selected="selected"/>
</template>
