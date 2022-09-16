<script setup>
    import {computed, defineProps} from 'vue'

    const props = defineProps({field, item})
    const value = computed(() => props.item[props.field.name])
</script>

<template>
    <td>
        {{ value }}
    </td>
</template>
