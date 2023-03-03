<script setup>
    import {defineEmits, defineProps} from 'vue'

    defineProps({
        fields: {required: true, type: Array}
    })
    const emit = defineEmits(['annuleAjout'])

    function AnnuleAjout(){
        emit('annuleAjout')
    }
</script>

<template>
    <td>
        <button class="btn btn-icon btn-primary btn-sm mx-2">
            <Fa icon="check"/>
        </button>
        <button class="btn btn-danger btn-icon btn-sm" @click="AnnuleAjout">
            <Fa icon="times"/>
        </button>
    </td>
    <td v-for="raw in fields" :key="raw.name">
        <AppInputGuesser v-if="raw.updateVisible" :field="raw"/>
    </td>
</template>
