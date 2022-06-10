<script setup>
    import {NodeRepository} from '../../store/modules'
    import {computed} from 'vue'
    import {useRepo} from '../../composition'

    const props = defineProps({node: {required: true, type: Object}})
    const repo = useRepo(NodeRepository)
    const bg = computed(() => ({'bg-warning': props.node.selected}))

    function click() {
        repo.select(props.node.id)
    }
</script>

<template>
    <span :class="bg" class="pointer" @click="click">
        <slot>
            <span class="pe-4"/>
        </slot>
        <Fa icon="folder"/>
        {{ node.label }}
    </span>
</template>
