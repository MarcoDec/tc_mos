<script setup>
    import AppTableFields from './field/AppTableFields.vue'

    defineProps({
        fields: {required: true, type: Array},
        send: {required: true, type: Function},
        store: {required: true, type: Object}
    })
</script>

<template>
    <thead class="table-dark">
        <AppTableFields :fields="fields" :send="send" :store="store"/>
    </thead>
</template>
