<script setup>
    import {inject, ref} from 'vue'
    import AppCollectionTableCreate from './AppCollectionTableCreate.vue'
    import AppCollectionTableFields from './AppCollectionTableFields.vue'
    import AppCollectionTableSearch from './AppCollectionTableSearch.vue'
    import {FiniteStateMachineRepository} from '../../../store/modules'
    import {useRepo} from '../../../composition'

    defineProps({coll: {required: true, type: Object}, violations: {default: () => [], type: Array}})

    const emit = defineEmits(['create', 'search'])
    const searchMode = inject('searchMode', ref(true))
    const updated = inject('updated', ref(-1))
    const stateMachine = inject('stateMachine')
    const stateRepo = useRepo(FiniteStateMachineRepository)

    function create(createOptions) {
        emit('create', createOptions)
    }

    function search() {
        emit('search')
    }

    function toggle() {
        stateRepo.reset(stateMachine)
        searchMode.value = !searchMode.value
        if (!searchMode.value)
            updated.value = -1
    }
</script>

<template>
    <thead class="table-dark">
        <AppCollectionTableFields :coll="coll" @click="search"/>
        <AppCollectionTableSearch v-if="searchMode" :coll="coll" @search="search" @toggle="toggle"/>
        <AppCollectionTableCreate v-else :violations="violations" @create="create" @toggle="toggle"/>
    </thead>
</template>
