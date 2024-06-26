<script setup>
    import {defineProps, provide} from 'vue'
    import AppTableHeaders from './head/AppTableHeaders.vue'
    import AppTableItems from './body/AppTableItems.vue'

    const props = defineProps({
        fields: {required: true, type: Object},
        item: {required: true, type: Object}
    })

    provide('fields', props.fields)
</script>

<template>
    <table class="table table-bordered table-hover table-striped">
        <AppTableHeaders/>
        <AppTableItems :item="item"/>
    </table>
</template>
