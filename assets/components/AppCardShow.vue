<script setup>
    import AppBtnJS from './AppBtnJS'
    import AppFormJS from './form/AppFormJS'
    import {ref} from 'vue'

    defineProps({
        componentAttribute: {required: true, type: Object},
        // disabled: {type: Boolean},
        fields: {default: () => [], type: Array},
        id: {required: true, type: String}

    })
    const emit = defineEmits(['update', 'update:modelValue'])
    const updated = ref(false)
    const disable = ref(true)
    function update() {
        updated.value = true
        disable.value = false
    }
    function success() {
        emit('update')
        updated.value = false
        disable.value = true
    }
    function annule() {
        updated.value = false
        disable.value = true
    }
    function input(value) {
        emit('update:modelValue', value)
    }
</script>

<template>
    <div class="card">
        <div class="bg-secondary card-header">
            <div v-if="!updated">
                <AppBtnJS icon="pencil-alt" variant="primary" @click="update"/>
            </div>
            <div v-else>
                <AppBtnJS icon="check" variant="success" @click="success"/>
                <AppBtnJS icon="times" variant="danger" @click="annule"/>
            </div>
        </div>
        <ul v-if="disable" class="card-body">
            <AppFormJS :id="id" :fields="fields" :model-value="componentAttribute" disabled/>
        </ul>
        <ul v-else class="card-body">
            <AppFormJS :id="id" :fields="fields" :model-value="componentAttribute" @update:model-value="input"/>
        </ul>
    </div>
</template>
