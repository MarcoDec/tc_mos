<script lang="ts" setup>
    import type {FormField, FormValues} from '../../types/bootstrap-5'
    import {computed, defineEmits, defineProps} from 'vue'

    const emit = defineEmits<(e: 'create', data: FormData) => void>()
    const props = defineProps<{fields: FormField[], id: string, values?: FormValues}>()
    const formId = computed(() => `${props.id}-form`)

    function submit(data: FormData): void {
        emit('create', data)
    }
</script>

<template>
    <AppCard :id="id" class="bg-blue">
        <AppForm :id="formId" :fields="fields" :model-value="values" @submit="submit">
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
