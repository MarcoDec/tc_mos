<script setup>
    import {computed, onMounted, onUnmounted, ref} from 'vue'
    import {Dropdown} from 'bootstrap'

    const dropdown = ref(null)
    const el = ref()
    const props = defineProps({id: {required: true, type: String}, tag: {default: 'div', type: String}})
    const liId = computed(() => `nav-${props.id}`)
    const dropdownId = computed(() => `${liId.value}-dropdown`)

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
    <component :is="tag" ref="el" class="dropdown">
        <slot :id="dropdownId" name="toggle"/>
        <ul :aria-labelledby="dropdownId" class="dropdown-menu dropdown-menu-dark">
            <slot/>
        </ul>
    </component>
</template>
