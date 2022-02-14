<script lang="ts" setup>
    import {computed, defineProps} from 'vue'
    import {useNamespacedGetters, useNamespacedState} from 'vuex-composition-helpers'
    import type {Ref} from 'vue'

    const props = defineProps<{modulePath: string}>()
    type Getters = {children: Ref<string[]>, hasChildren: Ref<boolean>}
    const {children, hasChildren} = useNamespacedGetters(props.modulePath, ['children', 'hasChildren']) as Getters
    const opened = useNamespacedState(props.modulePath, ['opened']).opened as Ref<boolean>
    const tag = computed(() => (hasChildren.value ? 'AppTreeClickableItem' : 'AppTreeItem'))
</script>

<template>
    <div>
        <component :is="tag" :module-path="modulePath"/>
        <AppTree v-for="child in children" v-show="opened" :key="child" :module-path="child" class="ms-4"/>
    </div>
</template>
