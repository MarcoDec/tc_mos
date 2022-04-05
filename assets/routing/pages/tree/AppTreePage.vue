<script lang="ts" setup>
    import type {Actions, Getters} from '../../../store/tree'
    import {computed, defineProps, inject, onMounted, provide, ref, withDefaults} from 'vue'
    import {useNamespacedActions, useNamespacedGetters} from 'vuex-composition-helpers'
    import type {FormField} from '../../../types/bootstrap-5'

    const moduleName = inject('moduleName', '')
    const load = useNamespacedActions<Actions>(moduleName, ['load']).load
    const loaded = ref(false)
    const options = useNamespacedGetters<Getters>(moduleName, ['options']).options
    const props = withDefaults(defineProps<{extraFields?: FormField[]}>(), {extraFields: () => []})
    // eslint-disable-next-line @typescript-eslint/ban-ts-comment
    // @ts-ignore
    const fields = computed<FormField[]>(() => [
        {label: 'Parent', name: 'parent', options: options.value, type: 'select'},
        {label: 'Nom', name: 'name', type: 'text'},
        {label: 'Code douanier', name: 'customsCode', type: 'text'},
        {label: 'Icône', name: 'file', type: 'file'},
        ...props.extraFields
    ])

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
            Familles de
            <slot/>
        </h1>
    </AppRow>
    <AppTreeRow v-if="loaded" id="tree"/>
</template>
