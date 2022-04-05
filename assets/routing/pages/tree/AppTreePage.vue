<script setup>
    import {computed, inject, onMounted, provide, ref} from 'vue'
    import {useNamespacedActions, useNamespacedGetters} from 'vuex-composition-helpers'

    const moduleName = inject('moduleName', '')
    const load = useNamespacedActions(moduleName, ['load']).load
    const loaded = ref(false)
    const options = useNamespacedGetters(moduleName, ['options']).options
    const props = defineProps({extraFields: {default: () => [], type: Array}})
    const fields = computed(() => [
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
