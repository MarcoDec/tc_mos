<script setup>
    import {defineProps} from 'vue'

    /*const props =*/ defineProps({
        field: {required: true, type: Object}
    })
</script>

<template>
    <th :width="field.width">
        <span class="d-flex justify-content-between">
            <span>{{ field.label }}</span>
        </span>
    </th>
</template>
