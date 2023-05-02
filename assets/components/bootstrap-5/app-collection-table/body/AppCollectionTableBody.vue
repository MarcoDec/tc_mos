<script setup>
    import {defineEmits, defineProps, ref} from 'vue'
    import AppCollectionTableItem from './AppCollectionTableItem.vue'
    import AppCollectionTableUpdateItem from './AppCollectionTableUpdateItem.vue'

    defineProps({
        fields: {required: true, type: Array},
        item: {required: true, type: Object},
    })
    const ajout = ref(false)
    const emit = defineEmits(['deleted', 'update','ajoute'])
    function update(item){
        emit('update', item)
    }
    function deleted(id){
        emit('deleted', id)
    }
    function ajoute() {
        emit('ajoute')
        ajout.value = true
    }
    function AnnuleAjout() {
        ajout.value = false
    }
</script>
 
<template>
    <AppCollectionTableItem v-if="!ajout" :fields="fields" :item="item" @ajoute="ajoute" @deleted="deleted"/>
    <AppCollectionTableUpdateItem v-else :fields="fields" :item="item" @update="update" :model-value="item" @annule-ajout="AnnuleAjout"/>
</template>
