<script setup>
    import AppCollectionTableField from './AppCollectionTableField.vue'
    import {inject} from 'vue'

    defineProps({coll: {required: true, type: Object}})
    const emit = defineEmits(['click'])
    const fields = inject('fields', [])

    function click() {
        emit('click')
    }
</script>

<template>
    <tr>
        <th/>
        <th>Actions</th>
        <AppCollectionTableField v-for="field in fields" :key="field.name" :coll="coll" :field="field" @click="click"/>
    </tr>
</template>
