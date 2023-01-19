<script setup>
    import AppTableAdd from './AppTableAdd.vue'
    import AppTableFields from './field/AppTableFields.vue'
    import AppTableSearch from './AppTableSearch.vue'
    import {computed} from 'vue'
    import {useSlots} from '../../../composable/table'

    const props = defineProps({
        action: {type: Boolean},
        fields: {required: true, type: Object},
        id: {required: true, type: String},
        machine: {required: true, type: Object},
        store: {required: true, type: Object}
    })
    const add = computed(() => `${props.id}-add`)
    const search = computed(() => `${props.id}-search`)
    const {createSlots, searchSlots} = useSlots(props.fields.fields)
</script>

<template>
    <thead :id="id" class="table-dark">
        <AppTableFields :action="action" :fields="fields" :send="machine.send" :store="store"/>
        <AppTableAdd
            v-if="machine.state.value.matches('create')"
            :id="add"
            :fields="fields"
            :machine="machine"
            :store="store">
            <template v-for="s in createSlots" :key="s.name" #[s.name]="args">
                <slot :name="s.slot" v-bind="args"/>
            </template>
        </AppTableAdd>
        <AppTableSearch v-else-if="fields.search" :id="search" :fields="fields" :send="machine.send" :store="store">
            <template v-for="s in searchSlots" :key="s.name" #[s.name]="args">
                <slot :name="s.slot" v-bind="args"/>
            </template>
        </AppTableSearch>
    </thead>
</template>
