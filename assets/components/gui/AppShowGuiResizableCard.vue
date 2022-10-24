<script setup>
    /* eslint-disable vue/no-unused-properties */
    const props = defineProps({
        gui: {required: true, type: Object},
        height: {required: true, type: String},
        marginEnd: {default: null, type: String},
        marginTop: {default: null, type: String},
        variant: {required: true, type: String},
        width: {required: true, type: String}
    })

    function drag() {
        props.gui.drag()
    }

    function enableDrag() {
        props.gui.enableDrag()
    }
</script>

<template>
    <AppShowGuiCard v-bind="$props">
        <hr class="resizer" @click="enableDrag" @mousedown="drag"/>
        <slot/>
    </AppShowGuiCard>
</template>
