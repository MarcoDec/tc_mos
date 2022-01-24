<script lang="ts" setup>
    import type {Actions, Getters} from '../../../../../store/purchase/component/families'
    import type {Violation, Violations} from '../../../../../types/types'
    import {computed, onMounted, provide, ref} from 'vue'
    import {useNamespacedActions, useNamespacedGetters} from 'vuex-composition-helpers'
    import type {FormField} from '../../../../../types/bootstrap-5'

    const {create, load} = useNamespacedActions<Actions>('families', ['create', 'load'])
    const {options, tree} = useNamespacedGetters<Getters>('families', ['options', 'tree'])
    const fields = computed<FormField[]>(() => [
        {label: 'Parent', name: 'parent', options: options.value, type: 'select'},
        {label: 'Code', name: 'code', type: 'text'},
        {label: 'Nom', name: 'name', type: 'text'},
        {label: 'Copperable', name: 'copperable', type: 'boolean'},
        {label: 'Customs Code', name: 'customsCode', type: 'text'},
        {label: 'file', name: 'file', type: 'file'}
    ])
    const violations = ref<Violation[]>([])

    async function createHandler(body: FormData): Promise<void> {
        violations.value = []
        try {
            await create(body)
        } catch (e) {
            if (e instanceof Response) {
                const json = await e.json() as Violations
                violations.value = json.violations as Violation[]
            }
        }
    }

    provide('violations', violations)
    onMounted(load)
</script>

<template>
    <AppRow>
        <h1 class="col">
            <Fa class="me-3" icon="layer-group"/>
            Familles
        </h1>
    </AppRow>
    <AppTreeRow id="component-families" :fields="fields" :item="tree" @create="createHandler"/>
</template>
