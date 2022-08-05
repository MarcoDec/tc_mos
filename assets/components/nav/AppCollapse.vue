<script setup>
    import {onMounted, onUnmounted, ref} from 'vue'
    import {Collapse} from 'bootstrap'

    const collapse = ref(null)
    const el = ref()

    function dispose() {
        if (collapse.value !== null) {
            collapse.value.dispose()
            collapse.value = null
        }
    }

    function instantiate() {
        if (el.value) {
            dispose()
            collapse.value = new Collapse(el.value)
        }
    }

    onMounted(instantiate)
    onUnmounted(dispose)
</script>

<template>
    <button
        ref="el"
        aria-controls="top-navbar"
        aria-expanded="false"
        aria-label="Toggle navigation"
        class="navbar-toggler"
        data-bs-target="#top-navbar"
        data-bs-toggle="collapse"
        type="button">
        <span class="navbar-toggler-icon"/>
    </button>
</template>
