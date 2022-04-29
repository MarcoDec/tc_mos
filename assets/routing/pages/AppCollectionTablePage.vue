<script setup>
    import {
        CollectionRepository,
        CountryRepository,
        EmployeeRepository,
        FiniteStateMachineRepository
    } from '../../store/modules'
    import {computed, onMounted, onUnmounted, ref} from 'vue'
    import {useRepo, useRouter} from '../../composition'
    import emitter from '../../emitter'

    const props = defineProps({
        brands: {type: Boolean},
        fields: {required: true, type: Array},
        icon: {required: true, type: String},
        repo: {required: true, type: Function},
        role: {required: true, type: String},
        title: {required: true, type: String}
    })
    const {id, route} = useRouter()
    const collRepo = useRepo(CollectionRepository)
    const countryRepo = useRepo(CountryRepository)
    const coll = computed(() => collRepo.find(id))
    const tableId = computed(() => `${id}-table`)
    const repoInstance = useRepo(props.repo)
    const items = computed(() => repoInstance.tableItems(props.fields, id))
    const stateRepo = useRepo(FiniteStateMachineRepository)
    const state = computed(() => stateRepo.find(id))
    const loading = computed(() => state.value?.loading ?? false)
    const violations = computed(() => state.value?.violations ?? [])
    const mount = ref(false)
    const userRepo = useRepo(EmployeeRepository)
    const user = computed(() => userRepo.user)
    const hasRole = computed(() => user.value[props.role])

    async function create(createOptions) {
        await repoInstance.create(createOptions, id)
    }

    async function deleteHandler(entityId) {
        await repoInstance.remove(entityId, id)
    }

    async function load() {
        await repoInstance.load(id)
    }

    async function pageHandler(page) {
        collRepo.setPage(page, id)
        await load()
    }

    async function update(item) {
        await repoInstance.update(item, id)
        emitter.emit(`${route.name}-update-${item.id ?? item.get('id')}`)
    }

    onMounted(async () => {
        stateRepo.create(id)
        await countryRepo.load()
        await load()
        mount.value = true
    })

    onUnmounted(() => {
        repoInstance.destroyAll(id)
        countryRepo.flush()
        stateRepo.destroy(id, id)
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
                :create="hasRole"
                :fields="fields"
                :items="items"
                :state-machine="id"
                :violations="violations"
                pagination
                @create="create"
                @delete="deleteHandler"
                @page="pageHandler"
                @search="load"
                @update="update"/>
        </div>
    </AppOverlay>
</template>
