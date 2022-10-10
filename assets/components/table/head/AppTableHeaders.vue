<script setup>
    import AppTableFields from './field/AppTableFields.vue'

    defineProps({fields: {required: true, type: Array}, store: {required: true, type: Object}})
</script>

<template>
    <thead class="table-dark">
        <AppTableFields :fields="fields" :store="store"/>
    </thead>
</template>
