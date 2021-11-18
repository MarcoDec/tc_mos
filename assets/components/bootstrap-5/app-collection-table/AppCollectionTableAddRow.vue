<script lang="ts" setup>
    import {defineEmits, defineProps} from 'vue'
    import type {FormField} from '../../../types/bootstrap-5'

    const props = defineProps<{addRaw: FormField}>()
    console.log('pppppp', props)

    const emit = defineEmits<(e: 'close') => void>()

    function bascule(): void{
        emit('close')
    }
</script>

<template>
    <tr id="addrow">
        <th scope="row" class="">
            <Fa icon="plus-circle"/>
        </th>
        <td>
            <button id="btn" class="btn btn-icon btn-secondary btn-sm mx-2" @click="bascule">
                <Fa icon="filter"/>
            </button>
            <button id="btn" class="btn btn-icon btn-sm btn-success">
                <Fa icon="plus-circle"/>
            </button>
        </td>
        <td v-for="raw in addRaw" :key="raw.name">
            <AppInput :field="raw"/>
        </td>
    </tr>
</template>

<style scoped>
#addrow{
    background-color:#c9ffc1 ;
}
#btn{
    width: 24px;
    height: 24px;
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
