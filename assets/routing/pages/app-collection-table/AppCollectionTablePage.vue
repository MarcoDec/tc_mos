<script setup>
    import {computed, onMounted, ref} from 'vue'
    import {useNamespacedActions, useNamespacedGetters} from 'vuex-composition-helpers'
    import {useRoute} from 'vue-router'

    const props = defineProps({
        fields: {required: true, type: Array},
        icon: {required: true, type: String},
        modulePath: {required: true, type: String},
        title: {required: true, type: String}
    })
    const loaded = ref(false)
    const tableItems = useNamespacedGetters(props.modulePath, ['tableItems']).tableItems
    const items = computed(() => (loaded.value ? tableItems.value(props.fields) : []))
    const load = useNamespacedActions(props.modulePath, ['load']).load
    const route = useRoute()

    onMounted(async () => {
        await load()
        loaded.value = true
    })
</script>

<template>
    <h1>
        <Fa :icon="icon"/>
        {{ title }}
    </h1>
    <AppCollectionTable :id="route.name" :fields="fields" :items="items" create pagination/>
</template>
