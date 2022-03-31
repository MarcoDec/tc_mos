<script setup>
    import {onMounted} from 'vue'
    import {useNamespacedActions} from 'vuex-composition-helpers'
    import {useRoute} from 'vue-router'

    const items = []
    const props = defineProps({
        fields: {required: true, type: Array},
        icon: {required: true, type: String},
        modulePath: {required: true, type: String},
        title: {required: true, type: String}
    })
    const load = useNamespacedActions(props.modulePath, ['load']).load
    const route = useRoute()

    onMounted(load)
</script>

<template>
    <h1>
        <Fa :icon="icon"/>
        {{ title }}
    </h1>
    <AppCollectionTable :id="route.name" :fields="fields" :items="items" create pagination/>
</template>
