<script setup>
    import {defineProps, onMounted, onUnmounted, ref} from 'vue'
    import {Modal} from 'bootstrap'

    const props = defineProps({
        noInstantiate: {required: false, type: Boolean},
        title: {required: true, type: String}
    })
    const el = ref()
    const modal = ref(null)

    function dispose() {
        if (modal.value !== null) {
            modal.value.dispose()
            modal.value = null
        }
    }

    function instantiate() {
        if (!props.noInstantiate && el.value) {
            dispose()
            modal.value = new Modal(el.value)
        }
    }

    onMounted(instantiate)

    onUnmounted(dispose)
</script>

<template>
    <div ref="el" class="modal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        {{ title }}
                    </h5>
                </div>
                <div class="modal-body">
                    <slot/>
                </div>
                <div class="modal-footer">
                    <AppBtn data-bs-dismiss="modal" label="creer" variant="success">
                        Créer
                    </AppBtn>
                    <AppBtn data-bs-dismiss="modal" label="fermer" variant="danger">
                        Fermer
                    </AppBtn>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
.modal-content{
    width: 160%;
}
</style>
