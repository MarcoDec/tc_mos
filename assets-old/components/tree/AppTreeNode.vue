<script setup>
    import AppTreeLabel from './AppTreeLabel.vue'
    import AppTreeVertex from './AppTreeVertex'
    import {computed} from 'vue'

    const props = defineProps({node: {required: true, type: Object}})
    const children = computed(() => [...props.node.children].sort((a, b) => a.label.localeCompare(b.label)))
    const tag = computed(() => (props.node.hasChildren ? AppTreeVertex : AppTreeLabel))
</script>

<template>
    <div>
        <component :is="tag" :node="node"/>
        <AppTreeNode v-for="child in children" v-show="node.opened" :key="child.id" :node="child" class="ms-4"/>
    </div>
</template>
