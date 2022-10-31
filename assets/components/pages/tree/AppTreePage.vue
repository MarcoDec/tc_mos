<script setup>
    import AppTree from '../../tree/AppTree.vue'
    import {onUnmounted} from 'vue'
    import {useRoute} from 'vue-router'
    import useTree from '../../../stores/tree/tree'

    defineProps({fields: {required: true, type: Array}, label: {required: true, type: String}})

    const route = useRoute()
    const tree = useTree(route.name)
    await tree.fetch()

    onUnmounted(() => {
        tree.dispose()
    })
</script>

<template>
    <div class="row">
        <h1 class="col">
            <Fa icon="layer-group"/>
            Familles de {{ label }}
        </h1>
    </div>
    <AppTree :fields="fields" :tree="tree"/>
</template>
