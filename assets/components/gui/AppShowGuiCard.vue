<script lang="ts" setup>
    /* eslint-disable vue/no-unused-properties */
    import {computed, defineProps} from 'vue'
    import type {BootstrapVariant} from '../../types/bootstrap-5'
    import type {PropType} from 'vue'

    const props = defineProps({
        bgVariant: {required: true, type: String as PropType<BootstrapVariant>},
        height: {required: true, type: String},
        marginEnd: {default: '0px', type: String},
        marginTop: {default: '0px', type: String},
        width: {required: true, type: String}
    })
    const bg = computed(() => `bg-${props.bgVariant}`)
</script>

<template>
    <div :class="bg" class="gui-card">
        <slot/>
    </div>
</template>

<style scoped>
    .gui-card {
        height: v-bind('height');
        margin-right: v-bind('marginEnd');
        margin-top: v-bind('marginTop');
        width: v-bind('width');
    }
</style>
