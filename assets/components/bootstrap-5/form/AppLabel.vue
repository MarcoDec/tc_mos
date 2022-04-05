<script setup>
    import {colValidator as validator} from '../../../composition/bootstrap-5'

    defineProps({cols: {default: 2, type: Number, validator}})
</script>

<template>
    <AppCol :cols="cols" class="form-label" tag="label">
        <slot/>
    </AppCol>
</template>
