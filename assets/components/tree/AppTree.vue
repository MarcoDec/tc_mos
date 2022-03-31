<script setup>
    import {useNamespacedGetters, useNamespacedState} from 'vuex-composition-helpers'
    import {computed} from 'vue'

    const props = defineProps({modulePath: {required: true, type: String}})
    const {children, hasChildren} = useNamespacedGetters(props.modulePath, ['children', 'hasChildren'])
    const opened = useNamespacedState(props.modulePath, ['opened']).opened
    const tag = computed(() => (hasChildren.value ? 'AppTreeClickableItem' : 'AppTreeItem'))
</script>

<template>
    <div>
        <component :is="tag" :module-path="modulePath"/>
        <AppTree v-for="child in children" v-show="opened" :key="child" :module-path="child" class="ms-4"/>
    </div>
</template>
