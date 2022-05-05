<script setup>
    import {computed, onMounted, onUnmounted, ref} from 'vue'
    import AppCollectionTablePage from './AppCollectionTablePage.vue'
    import {useRouter} from '../../composition'
    import {useStore} from 'vuex'

    const props = defineProps({
        brands: {type: Boolean},
        fields: {required: true, type: Array},
        icon: {required: true, type: String},
        repo: {required: true, type: Function},
        role: {required: true, type: String},
        title: {required: true, type: String}
    })
    const collectionFields = computed(() => props.fields.filter(field => field.collection))
    const {id} = useRouter()
    const form = computed(() => `${id}-create`)
    const noCollectionFields = computed(() => props.fields.filter(field => !field.collection))
    const optionRepos = computed(() => {
        const repos = []
        for (const field of noCollectionFields.value)
            if (typeof field.repo === 'function')
                repos.push(field.repo)
        return repos
    })
    const store = useStore()
    const optionId = computed(() => `${id}-options`)
    const mount = ref(false)

    onMounted(async () => {
        const options = []
        for (const repo of optionRepos.value)
            options.push(store.$repo(repo).loadOptions(optionId.value))
        await Promise.all(options)
        mount.value = true
    })

    onUnmounted(() => {
        for (const repo of optionRepos.value) {
            const optionRepo = store.$repo(repo)
            if (typeof optionRepo.destroyAll === 'function')
                optionRepo.destroyAll(optionId.value)
            else
                optionRepo.flush()
        }
    })
</script>

<template>
    <AppCollectionTablePage
        v-if="mount"
        :brands="brands"
        :fields="collectionFields"
        :icon="icon"
        :repo="repo"
        :role="role"
        :title="title">
        <template #top>
            <div>
                <AppBtn variant="success">
                    <Fa icon="plus"/>
                    Ajouter
                </AppBtn>
            </div>
        </template>
        <AppCol>
            <AppCard class="bg-blue" title="Ajouter">
                <AppForm :id="form" :fields="fields" :state-machine="id"/>
            </AppCard>
        </AppCol>
    </AppCollectionTablePage>
</template>
