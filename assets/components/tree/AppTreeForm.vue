<script lang="ts" setup>
    import type {FormField, FormValues} from '../../types/bootstrap-5'
    import {computed, defineProps} from 'vue'

    const props = defineProps<{fields: FormField[], id: string, values?: FormValues}>()
    const formId = computed(() => `${props.id}-form`)
</script>

<template>
    <AppCard :id="id" class="bg-blue">
        <AppForm :id="formId" :fields="fields" :model-value="values"/>
    </AppCard>
</template>
