<script setup>
    import {useNamespacedMutations, useNamespacedState} from 'vuex-composition-helpers'
    import {computed} from 'vue'

    const props = defineProps({modulePath: {required: true, type: String}})
    const opened = useNamespacedState(props.modulePath, ['opened']).opened
    const toggle = useNamespacedMutations(props.modulePath, ['toggle']).toggle
    const chevron = computed(() => `chevron-${opened.value ? 'up' : 'down'}`)
</script>

<template>
    <AppTreeItem :module-path="modulePath" @click="toggle">
        <Fa :icon="chevron" class="me-2"/>
    </AppTreeItem>
</template>
