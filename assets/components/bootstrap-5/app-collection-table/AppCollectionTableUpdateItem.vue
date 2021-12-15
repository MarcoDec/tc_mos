<script lang="ts" setup>
    import {computed, defineEmits, defineProps} from 'vue'
    import type {FormField} from '../../../types/bootstrap-5'
    import clone from 'clone'

    const props = defineProps<{fields: FormField}>()
    console.log('ppp', props)
    const tabFields = computed(() => props.fields.map(element => {
        const cloned = clone(element)

        if (cloned.type === 'boolean'){
            cloned.type = 'switch'
        }
        return cloned
    }))

    const emit = defineEmits<(e: 'annuleUpdate') => void>()

    function AnnuleUpdate(): void {
        emit('annuleUpdate')
    }
</script>

<template>
    <td>
        <button class="btn btn-icon btn-primary btn-sm mx-2">
            <Fa icon="check"/>
        </button>
        <button class="btn btn-danger btn-icon btn-sm" @click="AnnuleUpdate">
            <Fa icon="times"/>
        </button>
    </td>
    <td v-for="raw in tabFields" :key="raw.name">
        <AppInput v-if="raw.updateVisible" :field="raw"/>
    </td>
</template>
