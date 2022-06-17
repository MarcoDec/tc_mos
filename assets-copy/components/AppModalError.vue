<script lang="ts" setup>
    import type {Emitter, EventType} from 'mitt'
    import {inject, onMounted, onUnmounted, ref} from 'vue'
    import type {AxiosError} from 'axios'

    const message = ref<string | null>(null)
    const mitt = inject<Emitter<Record<EventType, AxiosError>>>('mitt')
    const show = ref<boolean>(false)

    function display(error: AxiosError): void {
        message.value = error.message
        show.value = true
    }

    function hide(): void {
        message.value = null
        show.value = false
    }

    onMounted(() => {
        mitt?.on('error', display)
    })

    onUnmounted(() => {
        mitt?.off('error', display)
    })
</script>

<template>
    <AppModal :show="show" title="Erreur" variant="danger" @hide="hide">
        <AppAlert variant="danger">
            {{ message }}
        </AppAlert>
    </AppModal>
</template>
