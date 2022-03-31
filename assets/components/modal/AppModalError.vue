<script setup>
    import {inject, onMounted, onUnmounted, ref} from 'vue'
    import {useMutations, useState} from 'vuex-composition-helpers'
    import {Modal} from 'bootstrap'

    const clean = useMutations(['responseError']).responseError
    const emitter = inject('emitter')
    const modal = ref(null)
    const props = defineProps({id: {required: true, type: String}})
    const {status: code, text} = useState(['status', 'text'])

    function dispose() {
        if (modal.value !== null) {
            modal.value.dispose()
            modal.value = null
        }
    }

    function instantiate() {
        dispose()
        const el = document.getElementById(props.id)
        if (el === null)
            return

        function destroy() {
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
