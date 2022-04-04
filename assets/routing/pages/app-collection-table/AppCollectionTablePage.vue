<script setup>
    import {computed, onMounted, ref} from 'vue'
    import {useNamespacedActions, useNamespacedGetters, useNamespacedState} from 'vuex-composition-helpers'
    import {useRoute} from 'vue-router'

    const props = defineProps({
        fields: {required: true, type: Array},
        icon: {required: true, type: String},
        modulePath: {required: true, type: String},
        title: {required: true, type: String}
    })
    const loading = ref(true)
    const tableItems = useNamespacedGetters(props.modulePath, ['tableItems']).tableItems
    const items = computed(() => (loading.value ? [] : tableItems.value(props.fields)))
    const {create, load, search} = useNamespacedActions(props.modulePath, ['create', 'load', 'search'])
    const route = useRoute()
    const violations = useNamespacedState(props.modulePath, ['violations']).violations

    async function createHandler(createOptions) {
        loading.value = true
        await create(createOptions)
        loading.value = false
    }

    onMounted(async () => {
        await load()
        loading.value = false
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
        @create="createHandler"
        @search="search"/>
</template>
