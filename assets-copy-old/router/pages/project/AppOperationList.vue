<script setup>
    import {useOpertionListStore} from '../../../stores/project/oprationList'
    import {useRoute} from 'vue-router'
    defineProps({
        fields: {default: () => [], type: Array},
        icon: {required: true, type: String},
        title: {required: true, type: String}
    })
    const route = useRoute()
    const form = `${route.name}-form`
    const storeOpertionList = useOpertionListStore()
    storeOpertionList.fetchItems()
</script>

<template>
    <h1>
        <Fa :icon="icon"/>
        {{ title }}
    </h1>
    <AppCollectionTable
        :form="form"
        :fields="fields"
        :items="storeOpertionList.items"
        :pag="true"/>
</template>
