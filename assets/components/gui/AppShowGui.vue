<script lang="ts" setup>
    import type {Getters, Mutations} from '../../store/gui'
    import {MutationTypes} from '../../store/gui'
    import {onMounted, onUnmounted, ref, watchPostEffect} from 'vue'
    import {useNamespacedGetters, useNamespacedMutations} from 'vuex-composition-helpers'

    const gui = ref<HTMLDivElement>()
    const {height, width} = useNamespacedGetters<Getters>('gui', ['height', 'width'])
    const {[MutationTypes.RESIZE]: resize} = useNamespacedMutations<Mutations>('gui', [MutationTypes.RESIZE])

    function resizeHandler(): void {
        if (typeof gui.value !== 'undefined')
            resize(gui.value)
    }

    watchPostEffect(() => {
        resizeHandler()
    })

    onMounted(() => {
        window.addEventListener('resize', resizeHandler)
    })

    onUnmounted(() => {
        window.removeEventListener('resize', resizeHandler)
    })
</script>

<template>
    <div class="overflow-hidden">
        <div ref="gui" class="bg-secondary gui"/>
    </div>
</template>

<style scoped>
    .gui {
        height: v-bind('height');
        max-height: v-bind('height');
        max-width: v-bind('width');
        min-height: v-bind('height');
        min-width: v-bind('width');
        width: v-bind('width')
    }
</style>
