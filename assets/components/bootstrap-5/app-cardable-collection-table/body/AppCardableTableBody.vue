<script setup>
    import {defineEmits, defineProps, ref} from 'vue'
    import AppCardableTableItem from './AppCardableTableItem.vue'
    import AppCardableTableUpdateItem from './AppCardableTableUpdateItem.vue'

    defineProps({
        fields: {required: true, type: Array},
        item: {required: true, type: Object}
    })

    const ajout = ref(false)
    const emit = defineEmits(['update'])

    function ajoute() {
        ajout.value = true
    }
    function AnnuleAjout() {
        ajout.value = false
    }
    function update(item){
        emit('update', item)
    }
</script>

<template>
    <AppCardableTableItem v-if="!ajout" :fields="fields" :item="item" @ajoute="ajoute" @update="update"/>
    <AppCardableTableUpdateItem v-else :fields="fields" @annule-ajout="AnnuleAjout"/>
</template>
