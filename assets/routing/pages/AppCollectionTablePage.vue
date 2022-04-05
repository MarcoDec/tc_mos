<script setup>
    import {computed, onMounted, ref} from 'vue'
    import emitter from '../../emitter'
    import {useRoute} from 'vue-router'
    import {useStore} from 'vuex'

    const props = defineProps({
        fields: {required: true, type: Array},
        icon: {required: true, type: String},
        repo: {required: true, type: Function},
        title: {required: true, type: String}
    })
    const route = useRoute()
    const store = useStore()
    const violations = ref([])

    const repoInstance = computed(() => store.$repo(props.repo))
    const items = computed(() => repoInstance.value.tableItems(props.fields))

    async function create(createOptions) {
        violations.value = await repoInstance.value.create(createOptions)
    }

    async function deleteHandler(id) {
        await repoInstance.value['delete'](id)
    }

    async function search(searchOptions) {
        await repoInstance.value.load(searchOptions)
    }

    async function update(item) {
        violations.value = await repoInstance.value.update(item)
        emitter.emit(`${route.name}-update-${item.get('id')}`)
    }

    onMounted(async () => {
        await repoInstance.value.load()
    })
</script>

<template>
    <h1>
        <Fa :icon="icon"/>
        {{ title }}
    </h1>
    <AppCollectionTable
        :id="route.name"
        :fields="fields"
        :items="items"
        :violations="violations"
        create
        pagination
        @create="create"
        @delete="deleteHandler"
        @search="search"
        @update="update"/>
</template>
