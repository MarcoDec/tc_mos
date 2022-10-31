<script setup>
    import AppTreeVertex from './AppTreeVertex.vue'
    import {computed} from 'vue'

    const props = defineProps({node: {required: true, type: Object}})
    const label = computed(() => (props.node.hasChildren ? AppTreeVertex : 'AppTreeLabel'))
</script>

<template>
    <div>
        <component :is="label" :node="node"/>
        <AppTreeNodes v-if="node.hasChildren" v-show="node.opened" :nodes="node.children" class="ms-4"/>
    </div>
</template>
