<script lang="ts" setup>
    import {defineProps} from 'vue'

    defineProps({
        cols: {
            default: 2,
            type: Number,
            validator: (value: number): boolean => value >= 1 && value <= 12
        }
    })
</script>

<template>
    <AppCol :cols="cols" class="form-label" tag="label">
        <slot/>
    </AppCol>
</template>
