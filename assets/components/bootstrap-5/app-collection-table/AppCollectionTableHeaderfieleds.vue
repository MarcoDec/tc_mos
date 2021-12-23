<script lang="ts" setup>
    import {defineEmits, defineProps} from 'vue'
    import type {FormField} from '../../../types/bootstrap-5'

    const emit = defineEmits<(e: 'trierAlphabet') => void>()
    defineProps<{fields: FormField[], trier?: string}>()

    function trierAlpha(): void {
        emit('trierAlphabet')
    }
</script>

<template>
    <template v-if="trier === 'both'">
        <template v-for="(field, index) in fields" :key="index">
            <th v-if="field.trie" scope="col" class="both sortable" @click="trierAlpha">
                {{ field.label }}
            </th>

            <th v-else scope="col" class="sortable" @click="trierAlpha">
                {{ field.label }}
            </th>
        </template>
    </template>
    <template v-else-if="trier === 'asc'">
        <template v-for="(field, index) in fields" :key="index">
            <th v-if="field.trie" scope="col" class="asc sortable" @click="trierAlpha">
                {{ field.label }}
            </th>

            <th v-else scope="col" class="sortable" @click="trierAlpha">
                {{ field.label }}
            </th>
        </template>
    </template>
    <template v-else>
        <template v-for="(field, index) in fields" :key="index">
            <th v-if="field.trie" scope="col" class="desc sortable" @click="trierAlpha">
                {{ field.label }}
            </th>

            <th v-else scope="col" class="sortable" @click="trierAlpha">
                {{ field.label }}
            </th>
        </template>
    </template>
</template>


