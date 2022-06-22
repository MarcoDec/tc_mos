<script setup>
    import AppTablePage from '../AppTablePage'
    import generateOptions from '../../../stores/options'
    import {onUnmounted} from 'vue'

    defineProps({icon: {required: true, type: String}, title: {required: true, type: String}})

    const options = generateOptions('units')
    await options.fetch()
    const fields = [
        {create: true, label: 'Code', name: 'code', search: true, sort: true, update: true},
        {create: true, label: 'Nom', name: 'name', search: true, sort: true, update: true},
        {create: true, label: 'Base', name: 'base', search: true, sort: true, type: 'number', update: true},
        {create: true, label: 'Parent', name: 'parent', options, search: true, sort: true, sortName: 'parent.code', type: 'select', update: true}
    ]

    onUnmounted(() => options.dispose())
</script>

<template>
    <AppTablePage :fields="fields" :icon="icon" :title="title"/>
</template>
