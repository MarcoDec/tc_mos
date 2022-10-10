<script setup>
    import AppTableField from './AppTableField.vue'

    defineProps({
        fields: {required: true, type: Array},
        send: {required: true, type: Function},
        store: {required: true, type: Object}
    })
</script>

<template>
    <tr>
        <th/>
        <AppTableField v-for="field in fields" :key="field.name" :field="field" :send="send" :store="store"/>
    </tr>
</template>
