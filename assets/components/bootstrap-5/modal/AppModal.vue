<script lang="ts" setup>
    import {computed, defineEmits, defineProps, onMounted, onUnmounted, ref, watch} from 'vue'
    import {Modal} from 'bootstrap'

    const emit = defineEmits<(e: 'hide') => void>()
    const props = defineProps<{show: boolean, title: string, variant?: string | null}>()

    const classVariant = computed<string | null>(() => (typeof props.variant !== 'undefined' && props.variant !== null && props.variant.length > 0
        ? `bg-${props.variant}`
        : null))
    const el = ref<HTMLDivElement>()
    const modal = ref<Modal | null>(null)

    function hide(): void {
        modal.value?.hide()
    }

    function onHide(): void {
        emit('hide')
    }

    watch(() => props.show, (show: boolean) => {
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
        if (el.value instanceof HTMLDivElement)
            el.value.removeEventListener('hidden.bs.modal', onHide)
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
