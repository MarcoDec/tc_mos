<script setup>
    import {computed, onMounted, onUnmounted, ref, shallowRef} from 'vue'
    import {Dropdown} from 'bootstrap'

    const dropdown = shallowRef(null)
    const el = ref()
    const props = defineProps({id: {required: true, type: String}})
    const dropdownId = computed(() => `${props.id}-dropdown`)

    function dispose() {
        if (dropdown.value !== null) {
            dropdown.value.dispose()
            dropdown.value = null
        }
    }

    function instantiate() {
        if (typeof el.value === 'undefined')
            return
        dispose()
        dropdown.value = new Dropdown(el.value)
    }

    onMounted(instantiate)
    onUnmounted(dispose)
</script>

<template>
    <div :id="id" ref="el" class="dropdown">
        <slot :id="dropdownId" name="toggle"/>
        <ul :aria-labelledby="dropdownId" class="dropdown-menu dropdown-menu-dark dropdown-menu-end">
            <slot/>
        </ul>
    </div>
</template>
