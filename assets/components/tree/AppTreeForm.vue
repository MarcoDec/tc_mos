<script lang="ts" setup>
    import type {ComputedRef, PropType, Ref} from 'vue'
    import type {FormField, FormValues} from '../../types/bootstrap-5'
    import {computed, defineEmits, defineProps, inject, provide} from 'vue'
    import {useNamespacedActions, useNamespacedState} from 'vuex-composition-helpers'

    const emit = defineEmits<(e: 'submit', data: FormData) => void>()
    const fields = inject<ComputedRef<FormField[]>>('fields', computed(() => []))
    const modulePath = inject<string>('modulePath')
    const parentModuleName = useNamespacedState(modulePath, ['parentModuleName']).parentModuleName as Ref<string>
    const create = useNamespacedActions(parentModuleName.value, ['create']).create as (body: FormData) => Promise<void>
    const props = defineProps({
        id: {required: true, type: String},
        state: {default: () => ({}), required: false, type: Object as PropType<FormValues>},
        title: {default: 'Créer une famille', required: false, type: String},
        update: {required: false, type: Boolean}
    })
    const formdId = computed(() => `${props.id}-form`)
    const violations = useNamespacedState(parentModuleName.value, ['violations']).violations

    async function submit(data: FormData): Promise<void> {
        if (props.update)
            emit('submit', data)
        else
            await create(data)
    }

    if (!props.update)
        provide('violations', violations)
</script>

<template>
    <AppCard :id="id" :title="title" class="bg-blue">
        <AppForm :id="formdId" :fields="fields" :model-value="state" @submit="submit">
            <template #start>
                <slot name="start"/>
            </template>
            <slot>
                <AppBtn type="submit" variant="success">
                    <Fa icon="plus"/>
                    Créer
                </AppBtn>
            </slot>
        </AppForm>
    </AppCard>
</template>
