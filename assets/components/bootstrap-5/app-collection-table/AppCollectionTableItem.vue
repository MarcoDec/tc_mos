<script lang="ts" setup>
    import {defineEmits, defineProps} from 'vue'
    import type {ItemField} from '../../../types/bootstrap-5'
    const props = defineProps<{item: ItemField}>()
    console.log('prp', props)


    const emit = defineEmits<(e: 'update') => void>()

    function update(): void {
        emit('update')
    }
</script>

<template>
    <td>
        <button v-if="!item.ajout" class="btn btn-icon btn-primary btn-sm mx-2" :disabled="item.ajout" @click="update">
            <Fa icon="pencil-alt"/>
        </button>
        <button v-if="!item.deletable" class="btn btn-danger btn-icon btn-sm mx-2" :disabled="item.deletable">
            <Fa icon="trash"/>
        </button>
    </td>
    <td>{{ item.code }}</td>
    <td>{{ item.name }}</td>
    <td> {{ item.type }} </td>
    <td>
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox"/>
        </div>
    </td>
    <td>{{ item.limite }}</td>
    <td>{{ item.cadence }}</td>
    <td> {{ item.prix }}</td>
    <td> {{ item.temps }}</td>
</template>
