<script  setup>
    import AppScheduleTableFields from './AppScheduleTableFields.vue'
    import {defineProps} from 'vue'
    defineProps({
        fields: {required: true, type: Array},
        lengthFields: {required: true, type: Number}
    })
</script>

<template>
    <thead>
        <AppScheduleTableFields :fields="fields" :length-fields="lengthFields"/>
    </thead>
</template>
