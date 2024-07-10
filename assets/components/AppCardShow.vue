<script setup>
    import AppBtnJS from './AppBtnJS'
    import {default as AppForm} from './form/AppFormJS'
    import {onBeforeMount, ref} from 'vue'

    const props = defineProps({
        componentAttribute: {required: true, type: Object},
        fields: {default: () => [], type: Array},
        id: {required: true, type: String},
        title: {default: '', type: String}
    })
    // console.log('AppCardShow props', props)
    const emit = defineEmits(['cancel', 'update', 'update:modelValue'])
    const updated = ref(false)
    const disable = ref(true)
    const localData = ref({})
    onBeforeMount(() => {
        localData.value = props.componentAttribute
        // console.log('localData', localData.value)
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
        // console.log('input', value)
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
            <AppForm :id="id" :fields="fields" :model-value="localData" disabled/>
        </ul>
        <ul v-else class="border-1 card-body">
            <AppForm :id="id" :fields="fields" :model-value="localData" @update:model-value="input"/>
        </ul>
    </div>
</template>

