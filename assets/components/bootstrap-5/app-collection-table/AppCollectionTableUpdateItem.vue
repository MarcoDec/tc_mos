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

    const emit = defineEmits<(e: 'annuleAjout') => void>()

    function AnnuleAjout(): void {
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
    <td v-for="raw in tabFields" :key="raw.name">
        <AppInput v-if="raw.updateVisible" :field="raw"/>
    </td>
</template>
