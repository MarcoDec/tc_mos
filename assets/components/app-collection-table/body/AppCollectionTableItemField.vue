<script setup>
    import {computed} from 'vue'

    const props = defineProps({
        field: {required: true, type: Object},
        item: {required: true, type: Object}
    })
    const value = computed(() => props.item[props.field.name])
</script>

<template>
    <td>
        {{ value }}
    </td>
</template>
