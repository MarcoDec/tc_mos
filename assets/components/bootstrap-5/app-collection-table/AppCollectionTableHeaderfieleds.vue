<script lang="ts" setup>
    import {defineEmits, defineProps} from 'vue'
    import type {FormField} from '../../../types/bootstrap-5'

    const emit = defineEmits<(e: 'trierAlphabet') => void>()
    const props = defineProps<{fields: FormField, trier?: string}>()

    console.log('pippip', props)
    function trierAlpha(): void {
        emit('trierAlphabet')
        console.log('emit')

    }
</script>

<template>
    <template v-if="trier === 'both'">
        <th v-for="(field, index) in fields" :key="index" scope="col" class="both sortable" @click="trierAlpha">
            {{ field.label }}
        </th>
    </template>
    <template v-else-if="trier === 'asc'">
        <th v-for="(field, index) in fields" :key="index" scope="col" class="asc sortable" @click="trierAlpha">
            {{ field.label }}
        </th>
    </template>
    <template v-else>
        <th v-for="(field, index) in fields" :key="index" scope="col" class="desc sortable" @click="trierAlpha">
            {{ field.label }}
        </th>
    </template>
</template>


