<script setup>
    import AppBtnJS from './AppBtnJS'
    import AppFormJS from './form/AppFormJS'
    import {onBeforeMount, ref} from 'vue'

    const props = defineProps({
        componentAttribute: {required: true, type: Object},
        fields: {default: () => [], type: Array},
        id: {required: true, type: String},
        title: {default: '', type: String}
    })
    const emit = defineEmits(['cancel', 'update', 'update:modelValue'])
    const updated = ref(false)
    const disable = ref(true)
    const localData = ref({})
    onBeforeMount(() => {
        localData.value = props.componentAttribute
    })
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
        emit('cancel')
    }
    function input(value) {
        localData.value = value
        emit('update:modelValue', value)
    }
</script>

<template>
    <div class="border-1 card">
        <div class="bg-secondary card-header pt-0 pb-0">
            <div>
                <AppBtnJS v-if="!updated" icon="pencil-alt" variant="primary" @click="update"/>
                <span v-else>
                    <AppBtnJS icon="check" variant="success" @click="success"/>
                    <AppBtnJS icon="times" variant="danger" @click="annule"/>
                </span>
                <span class="text-white">{{ title }}</span>
            </div>
        </div>
        <ul v-if="disable" class="card-body p-1">
            <AppFormJS :id="id" :fields="fields" :model-value="localData" disabled/>
        </ul>
        <ul v-else class="border-1 card-body">
            <AppFormJS :id="id" :fields="fields" :model-value="localData" @update:model-value="input"/>
        </ul>
    </div>
</template>

