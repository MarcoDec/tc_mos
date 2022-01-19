<script lang="ts" setup>
    import type {FormField, FormValues} from '../../types/bootstrap-5'
    import {defineProps} from 'vue'

    defineProps<{fields: FormField[], values?: FormValues}>()
</script>

<template>
    <AppCard class="bg-blue">
        <AppForm :fields="fields" :model-value="values"/>
    </AppCard>
</template>
