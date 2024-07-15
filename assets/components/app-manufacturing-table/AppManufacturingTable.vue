<script setup>
    import {defineProps, provide} from 'vue'
    import AppManufacturingTableHeaders from './head/AppManufacturingTableHeaders.vue'
    import AppManufacturingTableItems from './body/AppManufacturingTableItems.vue'
    const emits = defineEmits(['OFsConfirmed'])
    const props = defineProps({
        // currentPage: {default: 1, type: Number},
        fields: {required: true, type: Array},
        form: {required: true, type: String},
        id: {required: true, type: String},
        items: {required: true, type: Array},
        // pagination: {required: false, type: Boolean},
        title: {required: true, type: String}
    })
    // const count = computed(() => props.items.length)
    provide('fields', props.fields)
    provide('table-id', props.id)
    function onOFsConfirmed() {
        emits('OFsConfirmed')
    }
</script>

<template>
    <table :id="id" class="table table-bordered table-hover table-striped">
        <AppManufacturingTableHeaders :fields="fields"/>
        <AppManufacturingTableItems :id="id" :form="form" :items="items" :fields="fields" :title="title" @o-fs-confirmed="onOFsConfirmed"/>
    </table>
</template>
