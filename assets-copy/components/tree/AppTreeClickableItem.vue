<script lang="ts" setup>
    import type {Mutations, State} from '../../store/tree/item'
    import {computed, defineProps} from 'vue'
    import {useNamespacedMutations, useNamespacedState} from 'vuex-composition-helpers'

    const props = defineProps<{modulePath: string}>()
    const opened = useNamespacedState<State>(props.modulePath, ['opened']).opened
    const toggle = useNamespacedMutations<Mutations>(props.modulePath, ['toggle']).toggle
    const chevron = computed(() => `chevron-${opened.value ? 'up' : 'down'}`)
</script>

<template>
    <AppTreeItem :module-path="modulePath" @click="toggle">
        <Fa :icon="chevron" class="me-2"/>
    </AppTreeItem>
</template>
