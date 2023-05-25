<script setup>
    const emit = defineEmits(['trierAlphabet'])
    defineProps({
        field: {required: true, type: Object}
    })
    const trier = ref('both')
    function trierAlphabet(name) {
        if (trier.value === 'both'){
            trier.value = 'asc'
            emit('trierAlphabet', {name, trier})
        } else if (trier.value === 'asc'){
            trier.value = 'desc'
            emit('trierAlphabet', {name, trier})
        } else {
            trier.value = 'both'
            emit('trierAlphabet', {name, trier})
        }
    }
</script>

<template>
    <th v-if="field.trie" scope="col" :class="`${trier} sortable`" @click="trierAlphabet(field.name)">
        {{ field.label }}
    </th>
    <th v-else scope="col" class="sortable">
        {{ field.label }}
    </th>
</template>
