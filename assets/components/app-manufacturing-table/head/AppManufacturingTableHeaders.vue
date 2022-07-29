<script setup>
    import {defineProps} from 'vue'
    defineProps({ fields: { required: true, type: Array } })

</script>

<template>
    <thead class="table-dark">
        <AppManufacturingTableFields :fields="fields"/>
    </thead>
</template>
