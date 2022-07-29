<script  setup>
    import AppScheduleTableHeaders from './head/AppScheduleTableHeaders.vue'
    import AppScheduleTableItems from './body/AppScheduleTableItems.vue'
    import {computed, defineProps, provide} from 'vue'

    const props = defineProps({
        apiFields: {required: true, type: Object},
        currentPage: {default: 1, type: Number},
        fields: {required: true, type: Array},
        id: {required: true, type: String},
        items: {required: true, type: Array},
        lengthFields: {required: true, type: Number},
        pagination: {required: false, type: Boolean}
    })
    const count = computed(() => props.items.length)
    provide('fields', props.fields)
    provide('table-id', props.id)

    const listFields = [...props.fields]
    for (const field of props.apiFields){
        listFields.push(field)
    }
    
</script>

<template>
    <table :id="id">
        <AppScheduleTableHeaders :fields="listFields" :length-fields="lengthFields"/>
        <AppScheduleTableItems :items="items" :fields="listFields" :length-fields="lengthFields"/>
    </table>
    <!-- <AppPagination v-if="pagination" :count="count" :current="currentPage" class="float-end"/> -->
</template>
