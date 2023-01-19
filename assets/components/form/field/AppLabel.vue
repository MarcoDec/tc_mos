<script setup>
    defineProps({field: {required: true, type: Object}, input: {required: true, type: String}})
</script>

<template>
    <label :for="input" class="col-form-label col-md-3 col-xs-12">
        {{ field.label }}
    </label>
</template>
