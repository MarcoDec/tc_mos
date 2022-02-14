<script lang="ts" setup>
    import type {Actions, Getters} from '../../../../../store/purchase/component/families'
    import type {Violation, Violations} from '../../../../../types/types'
    import {onMounted, provide, ref} from 'vue'
    import {useNamespacedActions, useNamespacedGetters} from 'vuex-composition-helpers'
    import type {FormField} from '../../../../../types/bootstrap-5'

    const {create, load} = useNamespacedActions<Actions>('families', ['create', 'load'])
    const {options} = useNamespacedGetters<Getters>('families', ['options'])
    const fields: FormField[] = [
        {label: 'Parent', name: 'parent', options: options.value, type: 'select'},
        {label: 'Code', name: 'code', type: 'text'},
        {label: 'Nom', name: 'name', type: 'text'},
        {label: 'Copperable', name: 'copperable', type: 'boolean'},
        {label: 'Customs Code', name: 'customsCode', type: 'text'},
        {label: 'file', name: 'file', type: 'file'}
    ]
    const loaded = ref(false)
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

    provide('fields', fields)
    provide('violations', violations)
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
    <AppTreeRow v-if="loaded" id="component-families" @create="createHandler"/>
</template>
