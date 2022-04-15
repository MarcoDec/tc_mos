<script setup>
    import {computed, onMounted, onUnmounted, ref} from 'vue'
    import {useRepo, useRouter} from '../../composition'
    import {FiniteStateMachineRepository} from '../../store/modules'
    import emitter from '../../emitter'

    const props = defineProps({
        fields: {required: true, type: Array},
        icon: {required: true, type: String},
        repo: {required: true, type: Function},
        title: {required: true, type: String}
    })
    const {route} = useRouter()
    const id = computed(() => route.name)
    const tableId = computed(() => `${id.value}-table`)
    const violations = ref([])
    const repoInstance = useRepo(props.repo)
    const items = computed(() => repoInstance.tableItems(props.fields))
    const stateRepo = useRepo(FiniteStateMachineRepository)

    async function create(createOptions) {
        violations.value = await repoInstance.create(createOptions, id.value)
    }

    async function deleteHandler(entityId) {
        await repoInstance.remove(entityId, id.value)
    }

    async function search(searchOptions) {
        await repoInstance.load(searchOptions, id.value)
    }

    async function update(item) {
        violations.value = await repoInstance.update(item, id.value)
        emitter.emit(`${route.name}-update-${item.get('id')}`)
    }

    onMounted(async () => {
        stateRepo.create(id.value)
        await repoInstance.load(id.value)
    })

    onUnmounted(() => {
        stateRepo.destroy(id.value, id.value)
    })
</script>

<template>
    <div :id="id">
        <h1>
            <Fa :icon="icon"/>
            {{ title }}
        </h1>
        <AppCollectionTable
            :id="tableId"
            :fields="fields"
            :items="items"
            :state-machine="id"
            :violations="violations"
            create
            pagination
            @create="create"
            @delete="deleteHandler"
            @search="search"
            @update="update"/>
    </div>
</template>
