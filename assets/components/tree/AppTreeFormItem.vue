<script lang="ts" setup>
    import type {FormField, FormValues} from '../../types/bootstrap-5'
    import {computed, defineProps, inject} from 'vue'
    import {useNamespacedGetters, useNamespacedState} from 'vuex-composition-helpers'
    import type {Ref} from 'vue'

    const props = defineProps<{id: string, selected: string}>()
    const fields = inject<FormField[]>('fields', [])
    const label = useNamespacedGetters(props.selected, ['label']).label as Ref<string>
    const title = computed(() => `Modification de ${label.value}`)
    const state = useNamespacedState<FormValues>(props.selected, fields.map(({name}) => name))
    const stateValues = computed(() => {
        const values: FormValues = {}
        for (const property in state)
            values[property] = state[property].value
        return values
    })
</script>

<template>
    <AppTreeForm :id="id" :state="stateValues" :title="title"/>
</template>
