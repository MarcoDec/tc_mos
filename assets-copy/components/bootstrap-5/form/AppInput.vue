<script lang="ts" setup>
    import type {BootstrapSize, FormField, FormValue} from '../../../types/bootstrap-5'
    import {computed, defineEmits, defineProps, withDefaults} from 'vue'

    const emit = defineEmits<(e: 'update:modelValue', value: FormValue) => void>()
    const props = withDefaults(
        defineProps<{field: FormField, modelValue?: FormValue, size?: BootstrapSize, disabled: boolean}>(),
        {modelValue: null, size: 'sm'}
    )
    const sizeClass = computed(() => `form-control-${props.size}`)
    const type = computed(() => props.field.type ?? 'text')

    function input(e: Readonly<Event>): void {
        emit('update:modelValue', (e.target as HTMLInputElement).value)
    }
</script>

<template>
<<<<<<< HEAD
    <input
        :id="field.id"
        :class="sizeClass"
        :name="field.name"
        :placeholder="field.label"
        :type="type"
        :value="modelValue"
        :disabled="disabled"
        autocomplete="off"
        class="form-control"
        @input="input"/>
=======
    <input :class="sizeClass" :name="field.name" :type="type" :value="value" class="form-control" @input="input"/>
>>>>>>> feature/vue/warehouse
</template>
