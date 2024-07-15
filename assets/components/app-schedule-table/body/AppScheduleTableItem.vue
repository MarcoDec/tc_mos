<script setup>
    import {computed, defineProps} from 'vue'

    defineProps({
        fields: {required: true, type: Array},
        item: {required: true, type: Object},
        lengthFields: {required: true, type: Number}
    })
    const td = computed(() => 'AppScheduleTableItemField')
</script>

<template>
    <tr>
        <component :is="td" v-for="(field, index) in fields" :key="field.name" :index="index" :field="field" :item="item" :length-fields="lengthFields"/>
    </tr>
</template>
