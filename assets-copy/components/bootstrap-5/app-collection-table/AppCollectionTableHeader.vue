<script lang="ts" setup>
    import {defineProps, ref} from 'vue'
    import AppCollectionTableHeaderfieleds from './AppCollectionTableHeaderfieleds.vue'
    import type {FormField} from '../../../types/bootstrap-5'

    defineProps<{fields: FormField []}>()

    const trier = ref<string>('both')

    function trierAlphabet(): void{
        if (trier.value === 'both'){
            trier.value = 'desc'
        } else if (trier.value === 'desc'){
            trier.value = 'asc'
        } else {
            trier.value = 'both'
        }
    }
</script>

<template>
    <thead class="table-dark">
        <tr>
            <th scope="col"/>
            <th scope="col">
                Actions
            </th>
            <AppCollectionTableHeaderfieleds :trier="trier" :fields="fields" @trier-alphabet="trierAlphabet"/>
        </tr>
    </thead>
</template>
