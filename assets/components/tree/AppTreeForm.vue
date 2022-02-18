<script lang="ts" setup>
    import type {FormField, FormValues} from '../../types/bootstrap-5'
    import {computed, defineProps, inject, withDefaults} from 'vue'

    const props = withDefaults(
        defineProps<{id: string, state?: FormValues, title?: string}>(),
        {state: () => ({}), title: 'Créer une famille'}
    )
    const fields = inject<FormField[]>('fields', [])
    const formdId = computed(() => `${props.id}-form`)
</script>

<template>
    <AppCard :id="id" :title="title" class="bg-blue">
        <AppForm :id="formdId" :fields="fields" :model-value="state"/>
    </AppCard>
</template>
