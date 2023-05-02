<script setup>
    import AppCollectionTableHeaderfieleds from './AppCollectionTableHeaderfieleds.vue'
    import {defineProps} from 'vue'

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
            <AppCollectionTableHeaderfieleds :fields="fields" @trier-alphabet="trierAlphabet"/>
        </tr>
    </thead>
</template>
