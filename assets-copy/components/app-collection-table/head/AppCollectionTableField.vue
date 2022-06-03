<script setup>
    import AppCollectionTableFieldSimple from './AppCollectionTableFieldSimple'
    import AppCollectionTableFieldSorter from './AppCollectionTableFieldSorter.vue'
    import {computed} from 'vue'

    const emit = defineEmits(['click'])
    const props = defineProps({coll: {required: true, type: Object}, field: {required: true, type: Object}})
    const tag = computed(() => (props.field.sort ? AppCollectionTableFieldSorter : AppCollectionTableFieldSimple))

    function click() {
        if (props.field.sort)
            emit('click')
    }
</script>

<template>
    <component :is="tag" :coll="coll" :field="field" @click="click"/>
</template>
