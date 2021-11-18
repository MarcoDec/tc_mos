<script lang="ts" setup>
    import {defineEmits, defineProps} from 'vue'
    import type {FormField} from '../../../types/bootstrap-5'

    const props = defineProps<{addRaw: FormField}>()
    console.log('ppp', props)
    const emit = defineEmits<(e: 'annuleUpdate') => void>()

    function AnnuleUpdate(): void {
        emit('annuleUpdate')
    }
</script>

<template>
    <td>
        <button id="btn" class="btn btn-icon btn-primary btn-sm mx-2">
            <Fa icon="check"/>
        </button>
        <button id="btn" class="btn btn-danger btn-icon btn-sm" @click="AnnuleUpdate">
            <Fa icon="times"/>
        </button>
    </td>
    <td v-for="raw in addRaw" :key="raw.name">
        <AppInput :field="raw"/>
    </td>
</template>

<style scoped>
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
