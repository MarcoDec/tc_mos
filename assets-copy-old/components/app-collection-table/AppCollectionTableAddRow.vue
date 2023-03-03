<script setup>
    import {defineEmits, defineProps} from 'vue'

    defineProps({
        fields: {required: true, type: Array}
    })

    const emit = defineEmits(['close'])

    function bascule(){
        emit('close')
    }
</script>

<template>
    <tr class="addrow">
        <th scope="row">
            <Fa icon="plus-circle"/>
        </th>
        <td>
            <button class="btn btn-icon btn-secondary btn-sm mx-2" @click="bascule">
                <Fa icon="filter"/>
            </button>
            <button class="btn btn-icon btn-sm btn-success">
                <Fa icon="plus-circle"/>
            </button>
        </td>
        <td v-for="raw in fields" :key="raw.name">
            <AppInputGuesser v-if="raw.ajoutVisible" :field="raw"/>
        </td>
    </tr>
</template>

<style scoped>
.addrow{
    background-color:#c9ffc1 ;
}
.btn{
    width: 24px;
    height: 24px;
    padding-left: 4px;
    padding-bottom: 24px;
}
</style>
