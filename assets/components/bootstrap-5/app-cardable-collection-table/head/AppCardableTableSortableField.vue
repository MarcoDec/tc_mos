<script setup>
    import {ref} from 'vue'
    const emit = defineEmits(['trierAlphabet'])
    defineProps({
        field: {required: true, type: Object}
    })
    const trier = ref('both')
    function trierAlphabet(name) {
        if (trier.value === 'both'){
            trier.value = 'asc'
        } else if (trier.value === 'asc'){
            trier.value = 'desc'
        } else {
            trier.value = 'both'
        }
        emit('trierAlphabet', {direction: trier.value, name, trier})
    }
</script>

<template>
    <th v-if="field.trie" scope="col" :class="`${trier} sortable`" :style="{ width: field.width ? field.width + 'px' : null }" @click="trierAlphabet(field.name)">
        {{ field.label }}
    </th>
    <th v-else scope="col" class="sortable" :style="{ width: field.width ? field.width + 'px' : null }">
        {{ field.label }}
    </th>
</template>
