<script lang="ts" setup>
    import {computed, defineEmits, defineProps, onMounted, onUnmounted, ref, watch} from 'vue'
    import type {BootstrapVariant} from '../../../types/bootstrap-5'
    import {Modal} from 'bootstrap'

    const emit = defineEmits<(e: 'hide') => void>()
    const props = defineProps<{show: boolean, title: string, variant?: BootstrapVariant | null}>()

    const el = ref<HTMLDivElement>()
    const modal = ref<Modal | null>(null)

    const classVariant = computed(() => (
        typeof props.variant !== 'undefined' && props.variant !== null
            ? `bg-${props.variant}`
            : null
    ))

    function hide(): void {
        modal.value?.hide()
    }

    function onHide(): void {
        emit('hide')
    }

    watch(() => props.show, show => {
        if (show)
            modal.value?.show()
    })

    onMounted(() => {
        if (el.value instanceof HTMLDivElement) {
            modal.value = new Modal(el.value)
            el.value.addEventListener('hidden.bs.modal', onHide)
        }
    })

    onUnmounted(() => {
        el.value?.removeEventListener('hidden.bs.modal', onHide)
    })
</script>

<template>
    <div ref="el" class="fade modal" tabindex="-1">
        <div class="modal-dialog">
            <div :class="classVariant" class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title">
                        {{ title }}
                    </h1>
                    <button aria-label="Fermer" class="btn-close" type="button" @click="hide"/>
                </div>
                <div class="modal-body">
                    <slot/>
                </div>
            </div>
        </div>
    </div>
</template>
