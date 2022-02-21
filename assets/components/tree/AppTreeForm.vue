<script lang="ts" setup>
    import type {ComputedRef, Ref} from 'vue'
    import type {FormField, FormValues} from '../../types/bootstrap-5'
    import {computed, defineProps, inject, withDefaults} from 'vue'
    import {useNamespacedActions, useNamespacedState} from 'vuex-composition-helpers'

    const fields = inject<ComputedRef<FormField[]>>('fields', computed(() => []))
    const modulePath = inject<string>('modulePath')
    const parentModuleName = useNamespacedState(modulePath, ['parentModuleName']).parentModuleName as Ref<string>
    const create = useNamespacedActions(parentModuleName.value, ['create']).create as (body: FormData) => Promise<void>
    const props = withDefaults(
        defineProps<{id: string, state?: FormValues, title?: string}>(),
        {state: () => ({}), title: 'Créer une famille'}
    )
    const formdId = computed(() => `${props.id}-form`)
</script>

<template>
    <AppCard :id="id" :title="title" class="bg-blue">
        <AppForm :id="formdId" :fields="fields" :model-value="state" @submit="create">
            <slot>
                <AppBtn type="submit" variant="success">
                    <Fa icon="plus"/>
                    Créer
                </AppBtn>
            </slot>
        </AppForm>
    </AppCard>
</template>
