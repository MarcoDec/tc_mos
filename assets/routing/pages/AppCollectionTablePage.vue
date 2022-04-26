<script setup>
    import {CollectionRepository, CountryRepository, FiniteStateMachineRepository} from '../../store/modules'
    import {computed, onMounted, onUnmounted, ref} from 'vue'
    import {useRepo, useRouter} from '../../composition'
    import emitter from '../../emitter'

    const props = defineProps({
        brands: {type: Boolean},
        fields: {required: true, type: Array},
        icon: {required: true, type: String},
        repo: {required: true, type: Function},
        title: {required: true, type: String}
    })
    const {route} = useRouter()
    const collRepo = useRepo(CollectionRepository)
    const countryRepo = useRepo(CountryRepository)
    const id = computed(() => route.name)
    const coll = computed(() => collRepo.find(id.value))
    const tableId = computed(() => `${id.value}-table`)
    const repoInstance = useRepo(props.repo)
    const items = computed(() => repoInstance.tableItems(props.fields, id.value))
    const stateRepo = useRepo(FiniteStateMachineRepository)
    const state = computed(() => stateRepo.find(id.value))
    const loading = computed(() => state.value?.loading ?? false)
    const violations = computed(() => state.value?.violations ?? [])
    const mount = ref(false)

    async function create(createOptions) {
        await repoInstance.create(createOptions, id.value)
    }

    async function deleteHandler(entityId) {
        await repoInstance.remove(entityId, id.value)
    }

    async function load() {
        await repoInstance.load(id.value)
    }

    async function pageHandler(page) {
        collRepo.setPage(page, id.value)
        await load()
    }

    async function update(item) {
        await repoInstance.update(item, id.value)
        emitter.emit(`${route.name}-update-${item.id ?? item.get('id')}`)
    }

    onMounted(async () => {
        stateRepo.create(id.value)
        await countryRepo.load()
        await load()
        mount.value = true
    })

    onUnmounted(() => {
        stateRepo.destroy(id.value, id.value)
    })
</script>

<template>
    <AppOverlay :loading="loading">
        <div :id="id">
            <h1>
                <Fa :brands="brands" :icon="icon"/>
                {{ title }}
            </h1>
            <AppCollectionTable
                v-if="mount"
                :id="tableId"
                :coll="coll"
                :fields="fields"
                :items="items"
                :state-machine="id"
                :violations="violations"
                create
                pagination
                @create="create"
                @delete="deleteHandler"
                @page="pageHandler"
                @search="load"
                @update="update"/>
        </div>
    </AppOverlay>
</template>
