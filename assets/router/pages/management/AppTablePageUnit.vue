<script setup>
    import AppTablePage from '../AppTablePage'
    import generateOptions from '../../../stores/options'
    import {onUnmounted} from 'vue'

    defineProps({icon: {required: true, type: String}, title: {required: true, type: String}})

    const options = generateOptions('units')
    await options.fetch()
    const fields = [
        {label: 'Code', name: 'code', sort: true},
        {label: 'Nom', name: 'name', sort: true},
        {label: 'Base', name: 'base', sort: true, type: 'number'},
        {label: 'Parent', name: 'parent', options, sort: true, sortName: 'parent.code', type: 'select'}
    ]

    onUnmounted(() => options.dispose())
</script>

<template>
    <AppTablePage :fields="fields" :icon="icon" :title="title"/>
</template>
