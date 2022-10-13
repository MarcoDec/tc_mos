<script setup>
    import {computed} from 'vue'

    const props = defineProps({field: {required: true, type: Object}, item: {required: true, type: Object}})
    const value = computed(() => props.item[props.field.name])
    const label = computed(() => (props.field.type === 'select' ? props.field.options.label(value.value) : value.value))
    const isArray = computed(() => Array.isArray(label.value))
</script>

<template>
    <td>
        <ul v-if="isArray">
            <li v-for="(v, i) in label" :key="i">
                {{ v }}
            </li>
        </ul>
        <template v-else>
            {{ label }}
        </template>
    </td>
</template>
