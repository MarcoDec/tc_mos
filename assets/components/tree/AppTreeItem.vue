<script setup>
    import {useNamespacedActions, useNamespacedGetters, useNamespacedState} from 'vuex-composition-helpers'
    import {computed} from 'vue'

    const emit = defineEmits(['click'])
    const props = defineProps({modulePath: {required: true, type: String}})
    const label = useNamespacedGetters(props.modulePath, ['label']).label
    const select = useNamespacedActions(props.modulePath, ['select']).select
    const selected = useNamespacedState(props.modulePath, ['selected']).selected
    const bg = computed(() => ({'bg-warning': selected.value}))

    async function click() {
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
