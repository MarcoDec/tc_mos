<script setup>
    import {FiniteStateMachineRepository, NodeRepository} from '../../store/modules'
    import {computed, onMounted, onUnmounted} from 'vue'
    import {useRepo, useRouter} from '../../composition'
    import AppTreeForm from './AppTreeForm.vue'
    import AppTreeFormVertex from './AppTreeFormVertex.vue'
    import AppTreeNode from './AppTreeNode.vue'

    const {id} = useRouter()
    const nodeRepo = useRepo(NodeRepository)
    const props = defineProps({fields: {required: true, type: Array}, repo: {required: true, type: Function}})
    const form = computed(() => (nodeRepo.hasSelected ? AppTreeFormVertex : AppTreeForm))
    const stateRepo = useRepo(FiniteStateMachineRepository)
    const state = computed(() => stateRepo.find(id))
    const loading = computed(() => state.value?.loading ?? false)
    const tree = computed(() => [...nodeRepo.tree].sort((a, b) => a.label.localeCompare(b.label)))

    onMounted(async () => {
        stateRepo.create(id)
        await NodeRepository.load(props.repo, id)
    })

    onUnmounted(() => {
        nodeRepo.destroyAll(id, props.repo)
        stateRepo.destroy(id, id)
    })
</script>

<template>
    <AppRow :id="id">
        <div class="col">
            <AppOverlay :loading="loading">
                <AppTreeNode v-for="node in tree" :key="node.id" :node="node"/>
            </AppOverlay>
        </div>
        <component :is="form" :fields="fields" :repo="repo" class="col"/>
    </AppRow>
</template>
