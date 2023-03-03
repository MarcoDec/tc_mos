<script setup>
    import {defineProps, ref} from 'vue'
    import AppCardableTableHeaderfieleds from './AppCardableTableHeaderfieleds.vue'

    defineProps({
        fields: {required: true, type: Array}
    })

    const trier = ref('both')

    function trierAlphabet() {
        if (trier.value === 'both'){
            trier.value = 'desc'
        } else if (trier.value === 'desc'){
            trier.value = 'asc'
        } else {
            trier.value = 'both'
        }
    }
</script>

<template>
    <thead class="table-dark">
        <tr>
            <th scope="col"/>
            <th scope="col">
                Actions
            </th>
            <AppCardableTableHeaderfieleds :trier="trier" :fields="fields" @trier-alphabet="trierAlphabet"/>
        </tr>
    </thead>
</template>
