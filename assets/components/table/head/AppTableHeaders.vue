<script setup>
    import AppTableFields from './field/AppTableFields.vue'
    import AppTableSearch from './AppTableSearch.vue'
    import {computed} from 'vue'

    const props = defineProps({
        fields: {required: true, type: Array},
        id: {required: true, type: String},
        send: {required: true, type: Function},
        store: {required: true, type: Object}
    })
    const search = computed(() => `${props.id}-search`)
</script>

<template>
    <thead :id="id" class="table-dark">
        <AppTableFields :fields="fields" :send="send" :store="store"/>
        <AppTableSearch :id="search" :fields="fields" :send="send" :store="store"/>
    </thead>
</template>
