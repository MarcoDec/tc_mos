<script lang="ts" setup>
    import type {FormField, FormValue} from '../../../types/bootstrap-5'
    import {computed, defineProps} from 'vue'

    const props = defineProps<{field: FormField, id: string, modelValue?: FormValue}>()
    const options = computed(() => props.field.options ?? [])
</script>

<template>
    <select :id="id" :name="field.name" class="form-select">
        <AppSelectOption v-for="option in options" :option="option"/>
    </select>
</template>
