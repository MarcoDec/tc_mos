<script lang="ts" setup>
    import {computed, defineProps, inject} from 'vue'
    import type {FormField} from '../../types/bootstrap-5'

    const props = defineProps<{id: string}>()
    const fields = inject<FormField[]>('fields', [])
    const formdId = computed(() => `${props.id}-form`)
</script>

<template>
    <AppCard :id="id" class="bg-blue" title="Créer une famille">
        <AppForm :id="formdId" :fields="fields"/>
    </AppCard>
</template>
