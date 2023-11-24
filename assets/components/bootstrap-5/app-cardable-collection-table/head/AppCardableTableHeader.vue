<script setup>
    import AppCardableTableHeaderfieleds from './AppCardableTableHeaderfieleds.vue'

    const emit = defineEmits(['trierAlphabet'])

    defineProps({
        fields: {required: true, type: Array}
    })
    function trierAlphabet(payload) {
        emit('trierAlphabet', payload)
    }
</script>

<template>
    <thead class="table-dark">
        <tr>
            <th scope="col"/>
            <th scope="col">
                Actions
            </th>
            <AppCardableTableHeaderfieleds :fields="fields" @trier-alphabet="trierAlphabet"/>
        </tr>
    </thead>
</template>
