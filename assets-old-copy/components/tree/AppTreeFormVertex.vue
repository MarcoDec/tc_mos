<script setup>
    import AppTreeForm from './AppTreeForm.vue'
    import {NodeRepository} from '../../store/modules'
    import {computed} from 'vue'
    import {useRepo} from '../../composition'

    const props = defineProps({fields: {required: true, type: Array}, repo: {required: true, type: Function}})
    const nodeRepo = useRepo(NodeRepository)
    const selected = computed(() => nodeRepo.selected)
    const safeId = computed(() => selected.value.id)
    const form = computed(() => `${safeId.value}-form`)
    const relation = computed(() => selected.value.relation)

    async function click() {
        await NodeRepository.remove(relation.value.id, props.repo, form.value)
    }

    async function submit(body) {
        body.append('id', relation.value.id)
        await NodeRepository.update(body, props.repo, form.value)
    }

    function unselect() {
        nodeRepo.unselect()
    }
</script>

<template>
    <AppTreeForm
        :id="safeId"
        :key="safeId"
        :fields="fields"
        :model-value="relation"
        :repo="repo"
        success="Modifier"
        title="Modifier une famille"
        @submit="submit">
        <template #start>
            <AppBtn variant="danger" @click="unselect">
                <Fa icon="backward"/>
                Retour
            </AppBtn>
        </template>
        <AppBtn class="ms-2" variant="danger" @click="click">
            <Fa icon="trash"/>
            Supprimer
        </AppBtn>
    </AppTreeForm>
</template>
