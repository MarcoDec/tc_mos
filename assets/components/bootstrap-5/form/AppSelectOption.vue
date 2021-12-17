<script lang="ts" setup>
    import type {FormOption} from '../../../types/bootstrap-5'
    import {defineProps} from 'vue'

    defineProps<{option: FormOption}>()
</script>

<template>
    <option :value="option.value">
        {{ option.text }}
    </option>
</template>
