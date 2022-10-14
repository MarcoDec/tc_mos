<script setup>
    import AppTableField from './AppTableField.vue'

    defineProps({
        action: {type: Boolean},
        fields: {required: true, type: Object},
        send: {required: true, type: Function},
        store: {required: true, type: Object}
    })
</script>

<template>
    <tr>
        <th v-if="action">
            Actions
        </th>
        <th>Index</th>
        <AppTableField v-for="field in fields" :key="field.name" :field="field" :send="send" :store="store"/>
    </tr>
</template>
