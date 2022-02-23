<script lang="ts" setup>
    import {defineProps, ref} from 'vue'
    import type {FormField} from '../../types/bootstrap-5'
    defineProps<{fields: FormField[]}>()

    const updated = ref(false)
    const disable = ref(true)

    function update(): void {
        updated.value = true
        disable.value = false
    }
    function annule(): void {
        updated.value = false
        disable.value = true
    }
</script>

<template>
    <div class="card">
        <div class="bg-secondary card-header">
            <div v-if="!updated">
                <AppBtn icon="pencil-alt" variant="primary" @click="update"/>
            </div>
            <div v-else>
                <AppBtn icon="check" variant="success"/>
                <AppBtn icon="times" variant="danger" @click="annule"/>
            </div>
        </div>
        <ul v-if="disable" class="card-body">
            <AppForm :fields="fields" disabled/>
        </ul>
        <ul v-else class="card-body">
            <AppForm :fields="fields"/>
        </ul>
    </div>
</template>
