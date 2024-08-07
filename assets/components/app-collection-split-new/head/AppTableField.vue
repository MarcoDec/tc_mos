<script setup>
    import {defineEmits, defineProps} from 'vue'

    const emit = defineEmits('click')
    const props = defineProps({field: {required: true, type: Object}})

    function click() {
        emit('click', props.field)
    }
</script>

<template>
    <th @click="click">
        <span class="d-flex justify-content-between">
            <span>{{ field.label }}</span>
        </span>
    </th>
</template>
