<script lang="ts" setup>
    import type {BootstrapSize, FormField, FormValue} from '../../../types/bootstrap-5'
    import {computed, defineEmits, defineProps, withDefaults} from 'vue'

    const emit = defineEmits<(e: 'update:value', value: FormValue) => void>()
    const props = withDefaults(
        defineProps<{field: FormField, value?: FormValue, size?: BootstrapSize}>(),
        {size: 'sm', value: ''}
    )

    const sizeClass = computed(() => `form-control-${props.size}`)
    const type = computed(() => (typeof props.field.type !== 'undefined' ? props.field.type : 'text'))

    function input(e: Event): void {
        if (e instanceof InputEvent)
            emit('update:value', (e.target as HTMLInputElement).value)
    }
</script>

<template>
    <input :class="sizeClass" :name="field.name" :type="type" :value="value" class="form-control" @input="input"/>
</template>
