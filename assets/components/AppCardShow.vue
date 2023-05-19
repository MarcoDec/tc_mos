<script setup>
    import {defineProps, ref} from 'vue'
    import AppBtnJS from './AppBtnJS'
    import AppFormJS from './form/AppFormJS'

    defineProps({
        fields: {default: () => [], type: Array},
        id: {required: true, type: String}
    })
    const updated = ref(false)
    const disable = ref(true)

    function update() {
        updated.value = true
        disable.value = false
    }
    function annule() {
        updated.value = false
        disable.value = true
    }
</script>

<template>
    <div class="card">
        <div class="bg-secondary card-header">
            <div v-if="!updated">
                <AppBtnJS icon="pencil-alt" variant="primary" @click="update"/>
            </div>
            <div v-else>
                <AppBtnJS icon="check" variant="success"/>
                <AppBtnJS icon="times" variant="danger" @click="annule"/>
            </div>
        </div>
        <ul v-if="disable" class="card-body">
            <AppFormJS :id="id" :fields="fields" disabled/>
        </ul>
        <ul v-else class="card-body">
            <AppFormJS :id="id" :fields="fields"/>
        </ul>
    </div>
</template>
