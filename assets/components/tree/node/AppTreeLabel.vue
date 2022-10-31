<script setup>
    const props = defineProps({node: {required: true, type: Object}})

    function click() {
        props.node.focus()
    }
</script>

<template>
    <span :class="node.bg" class="pointer" @click="click">
        <slot>
            <span class="pe-2"/>
        </slot>
        <Fa icon="layer-group"/>
        {{ node.name }}
    </span>
</template>
