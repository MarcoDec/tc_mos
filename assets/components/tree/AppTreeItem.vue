<script lang="ts" setup>
    import {computed, defineEmits, defineProps} from 'vue'
    import {useNamespacedActions, useNamespacedGetters, useNamespacedState} from 'vuex-composition-helpers'
    import type {Ref} from 'vue'

    const emit = defineEmits<(e: 'click') => void>()
    const props = defineProps<{modulePath: string}>()
    const label = useNamespacedGetters(props.modulePath, ['label']).label
    const select = useNamespacedActions(props.modulePath, ['select']).select
    const selected = useNamespacedState(props.modulePath, ['selected']).selected as Ref<boolean>
    const bg = computed(() => ({'bg-warning': selected.value}))

    async function click(): Promise<void> {
        await select()
        emit('click')
    }
</script>

<template>
    <span :class="bg" @click="click">
        <slot>
            <span class="pe-4"/>
        </slot>
        <Fa icon="folder"/>
        {{ label }}
    </span>
</template>
