<script setup>
    import AppCardableTableHeaderfieleds from './AppCardableTableHeaderfieleds.vue'

    const emit = defineEmits(['trierAlphabet'])

    defineProps({
        fields: {required: true, type: Array},
        title: {default: null, required: false, type: String}
    })
    function trierAlphabet(payload) {
        emit('trierAlphabet', payload)
    }
</script>

<template>
    <thead class="table-dark">
        <tr v-if="title !== null">
            <td class="bg-secondary text-uppercase text-xl-center" :colspan="fields.length + 1">
                <slot name="title">
                    {{ title }}
                </slot>
            </td>
        </tr>
        <tr>
            <th scope="col">
                Actions
            </th>
            <AppCardableTableHeaderfieleds :fields="fields" @trier-alphabet="trierAlphabet"/>
        </tr>
    </thead>
</template>
