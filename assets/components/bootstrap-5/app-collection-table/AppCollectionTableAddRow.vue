<script lang="ts" setup>
    import {computed, defineEmits, defineProps} from 'vue'
    import type {FormField} from '../../../types/bootstrap-5'
    import clone from 'clone'

    const props = defineProps<{fields: FormField []}>()

    const tabFields = computed(() => props.fields.map(element => {
        const cloned = clone(element)

        if (cloned.type === 'boolean'){
            cloned.type = 'switch'
        }
        return cloned
    }))

    const emit = defineEmits<(e: 'close') => void>()

    function bascule(): void{
        emit('close')
    }
</script>

<template>
    <tr class="addrow">
        <th scope="row" class="">
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
        <td v-for="raw in tabFields" :key="raw.name">
            <AppInput v-if="raw.ajoutVisible" :field="raw"/>
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
