<script lang="ts" setup>
    import {computed, defineEmits, defineProps} from 'vue'
    import type {FormField} from '../../../types/bootstrap-5'

    const props = defineProps<{field: FormField, value?: number | string}>()
    const type = computed(() => (typeof props.field.type !== 'undefined' ? props.field.type : 'text'))
    const emit = defineEmits<(e: 'update:value', value: number | string) => void>()

    function input(e: Event): void {
        if (e instanceof InputEvent)
            emit('update:value', (e.target as HTMLInputElement).value)
    }
</script>

<template>
    <input class="form-control" :type="type" :name="field.name" :value="value" @input="input"/>
</template>
