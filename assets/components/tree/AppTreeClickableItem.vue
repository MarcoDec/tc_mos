<script lang="ts" setup>
    import {computed, defineProps} from 'vue'
    import {useNamespacedMutations, useNamespacedState} from 'vuex-composition-helpers'
    import type {Ref} from 'vue'

    const props = defineProps<{modulePath: string}>()
    const opened = useNamespacedState(props.modulePath, ['opened']).opened as Ref<boolean>
    const toggle = useNamespacedMutations(props.modulePath, ['toggle']).toggle
    const chevron = computed(() => `chevron-${opened.value ? 'up' : 'down'}`)
</script>

<template>
    <AppTreeItem :module-path="modulePath" @click="toggle">
        <Fa :icon="chevron" class="me-2"/>
    </AppTreeItem>
</template>
