<script lang="ts" setup>
    import {defineEmits, defineProps} from 'vue'
    import type {FormField} from '../../../types/bootstrap-5'

    const props = defineProps<{fields: FormField}>()
    console.log('ppp', props)

    const emit = defineEmits<(e: 'open') => void>()

    function ajout(): void {
        emit('open')
    }
</script>

<template>
    <tr id="header">
        <th scope="row" class="">
            <Fa icon="filter"/>
        </th>
        <td>
            <button id="btn" @click="ajout">
                <Fa id="icon" icon="plus-circle"/>
            </button>
            <button id="btn">
                <Fa id="icon" icon="search"/>
            </button>
            <button id="btntimes">
                <Fa id="icon" icon="times"/>
            </button>
        </td>

        <td v-for="field in fields" :key="field.name">
            <AppInput :field="field"/>
        </td>
    </tr>
</template>

<style scoped>
#header{
    background-color: #c5c5c5 ;
}
#btn{
    width: 24px;
    height: 24px;
    margin-left: 2px;
    margin-bottom: 4px;
    color: #fff;
    background-color: #6c757d;
    border-color: #6c757d;
    padding-left: 2px;
    padding-bottom: 24px;
}
#btntimes{
    width: 24px;
    height: 24px;
    margin-left: 2px;
    margin-bottom: 4px;
    color: #fff;
    background-color: #dc3545;
    border-color: #dc3545;
    padding-left: 4px;
    padding-bottom: 24px;
}

#inputCode {
    width: 160px;
}

#inputName{
    width: 300px;
}

#inputType{
    width: 50px;
}

#btnGroup{
    width: 100px;
}

#inputLimite{
    width: 500px;
}
</style>
