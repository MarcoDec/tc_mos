<script setup>
    import AppTablePage from '../AppTablePage'
    import generateOptions from '../../../stores/options'
    import {onUnmounted} from 'vue'

    defineProps({icon: {required: true, type: String}, title: {required: true, type: String}})

    const options = generateOptions('units')
    await options.fetch()
    const fields = [
        {label: 'Code', name: 'code'},
        {label: 'Nom', name: 'name'},
        {label: 'Base', name: 'base'},
        {label: 'Parent', name: 'parent', options, type: 'select'}
    ]

    onUnmounted(() => options.dispose())
</script>

<template>
    <AppTablePage :fields="fields" :icon="icon" :title="title"/>
</template>
