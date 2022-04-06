<script setup>
    import {computed, inject, provide, ref} from 'vue'
    import {useNamespacedActions, useNamespacedState} from 'vuex-composition-helpers'

    const emit = defineEmits(['submit'])
    const fields = inject('fields', computed(() => []))
    const moduleName = inject('moduleName', '')
    const create = useNamespacedActions(moduleName, ['create']).create
    const props = defineProps({
        id: {required: true, type: String},
        state: {default: () => ({}), required: false, type: Object},
        title: {default: 'Créer une famille', required: false, type: String},
        update: {required: false, type: Boolean}
    })
    const formdId = computed(() => `${props.id}-form`)
    const value = ref({...props.state})
    const violations = useNamespacedState(moduleName, ['violations']).violations

    async function submit(data) {
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
        <AppForm :id="formdId" v-model="value" :fields="fields" @submit="submit">
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
