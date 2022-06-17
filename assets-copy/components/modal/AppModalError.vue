<script lang="ts" setup>
    import type {Mutations, State} from '../../store'
    import {defineProps, inject, onMounted, onUnmounted, ref} from 'vue'
    import {useMutations, useState} from 'vuex-composition-helpers'
    import type {Emitter} from '../../emitter'
    import {Modal} from 'bootstrap'

    const clean = useMutations<Mutations>(['responseError']).responseError
    const emitter = inject<Emitter>('emitter')
    const modal = ref<Modal | null>(null)
    const props = defineProps<{id: string}>()
    const {status: code, text} = useState<State>(['status', 'text'])

    function dispose(): void {
        if (modal.value !== null) {
            modal.value.dispose()
            modal.value = null
        }
    }

    function instantiate(): void {
        dispose()
        const el = document.getElementById(props.id)
        if (el === null)
            return

        function destroy(): void {
            clean({status: 0, text: null})
            el?.removeEventListener('hidden.bs.modal', destroy)
            dispose()
        }

        el.addEventListener('hidden.bs.modal', destroy)
        modal.value = new Modal(el, {keyboard: false})
        modal.value.show()
    }

    onMounted(() => {
        emitter?.on('error', instantiate)
    })

    onUnmounted(() => {
        emitter?.off('error', instantiate)
        dispose()
    })
</script>

<template>
    <AppModal :id="id" no-instantiate title="Erreur">
        <AppAlert variant="danger">
            <AppBadge variant="danger">
                Erreur
                <template v-if="code">
                    {{ code }}
                </template>
            </AppBadge>
            {{ text }}
        </AppAlert>
    </AppModal>
</template>
