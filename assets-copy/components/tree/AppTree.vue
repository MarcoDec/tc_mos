<script lang="ts" setup>
    import type {Getters, State} from '../../store/tree/item'
    import {computed, defineProps} from 'vue'
    import {useNamespacedGetters, useNamespacedState} from 'vuex-composition-helpers'

    const props = defineProps<{modulePath: string}>()
    const {children, hasChildren} = useNamespacedGetters<Getters>(props.modulePath, ['children', 'hasChildren'])
    const opened = useNamespacedState<State>(props.modulePath, ['opened']).opened
    const tag = computed(() => (hasChildren.value ? 'AppTreeClickableItem' : 'AppTreeItem'))
</script>

<template>
    <div>
        <component :is="tag" :module-path="modulePath"/>
        <AppTree v-for="child in children" v-show="opened" :key="child" :module-path="child" class="ms-4"/>
    </div>
</template>
