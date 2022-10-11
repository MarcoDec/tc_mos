<script setup>
    import AppTableAdd from './AppTableAdd.vue'
    import AppTableFields from './field/AppTableFields.vue'
    import AppTableSearch from './AppTableSearch.vue'
    import {computed} from 'vue'

    const props = defineProps({
        fields: {required: true, type: Array},
        id: {required: true, type: String},
        machine: {required: true, type: Object},
        store: {required: true, type: Object}
    })
    const add = computed(() => `${props.id}-add`)
    const search = computed(() => `${props.id}-search`)
</script>

<template>
    <thead :id="id" class="table-dark">
        <AppTableFields :fields="fields" :send="machine.send" :store="store"/>
        <AppTableAdd
            v-if="machine.state.value.matches('create')"
            :id="add"
            :fields="fields"
            :machine="machine"
            :store="store"/>
        <AppTableSearch v-else :id="search" :fields="fields" :send="machine.send" :store="store"/>
    </thead>
</template>
