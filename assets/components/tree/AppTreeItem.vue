<script lang="ts" setup>
    import type {Actions, Getters, State} from '../../store/tree/item'
    import {computed, defineEmits, defineProps} from 'vue'
    import {useNamespacedActions, useNamespacedGetters, useNamespacedState} from 'vuex-composition-helpers'

    const emit = defineEmits<(e: 'click') => void>()
    const props = defineProps<{modulePath: string}>()
    const label = useNamespacedGetters<Getters>(props.modulePath, ['label']).label
    const select = useNamespacedActions<Actions>(props.modulePath, ['select']).select
    const selected = useNamespacedState<State>(props.modulePath, ['selected']).selected
    const bg = computed(() => ({'bg-warning': selected.value}))

    async function click(): Promise<void> {
        await select()
        emit('click')
    }
</script>

<template>
    <span :class="bg" class="pointer" @click="click">
        <slot>
            <span class="pe-4"/>
        </slot>
        <Fa icon="folder"/>
        {{ label }}
    </span>
</template>
