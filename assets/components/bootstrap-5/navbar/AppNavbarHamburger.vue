<script setup>
    import {onMounted, onUnmounted, ref} from 'vue'
    import {Collapse} from 'bootstrap'

    const btn = ref()
    const collapse = ref(null)

    function dispose() {
        if (collapse.value !== null) {
            collapse.value.dispose()
            collapse.value = null
        }
    }

    function instantiate() {
        if (typeof btn.value === 'undefined')
            return
        dispose()
        collapse.value = new Collapse(btn.value)
    }

    onMounted(instantiate)
    onUnmounted(dispose)
</script>

<template>
    <button
        ref="btn"
        aria-controls="app-top-navbar-collapse"
        aria-expanded="false"
        aria-label="Toggle navigation"
        class="navbar-toggler"
        data-bs-target="#app-top-navbar-collapse"
        data-bs-toggle="collapse"
        type="button">
        <span class="navbar-toggler-icon"/>
    </button>
</template>
