<script setup>
    import {computed} from 'vue'

    const props = defineProps({
        height: {required: true, type: String},
        innerWidth: {required: true, type: String},
        marginEnd: {default: '0px', type: String},
        marginTop: {default: '0px', type: String},
        variant: {required: true, type: String},
        width: {required: true, type: String}
    })
    const bg = computed(() => `bg-${props.variant}`)
</script>

<template>
    <div :class="bg" class="gui-card">
        <slot/>
    </div>
</template>

<style lang="scss" scoped>
    .gui-card {
        height: v-bind('height');
        margin-top: v-bind('marginTop');
        max-height: v-bind('height');
        min-height: v-bind('height');
    }

    @media (max-width: 1140px) {
        .gui-card {
            max-width: v-bind('innerWidth');
            min-width: v-bind('innerWidth');
            width: v-bind('innerWidth');
        }
    }

    @media (min-width: 1140px) {
        .gui-card {
            margin-right: v-bind('marginEnd');
            max-width: v-bind('width');
            min-width: v-bind('width');
            width: v-bind('width');
        }
    }
</style>
