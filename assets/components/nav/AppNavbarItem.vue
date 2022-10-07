<script setup>
    import {computed, onMounted, onUnmounted, ref, shallowRef} from 'vue'
    import {Dropdown} from 'bootstrap'

    const dropdown = shallowRef(null)
    const el = ref()
    const props = defineProps({
        icon: {required: true, type: String},
        id: {required: true, type: String},
        title: {required: true, type: String}
    })
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
    <li :id="id" ref="el" class="dropdown nav-item">
        <span
            :id="dropdownId"
            aria-expanded="false"
            class="dropdown-toggle m-0 ms-3 nav-link p-0"
            data-bs-toggle="dropdown"
            role="button">
            <Fa :icon="icon"/>
            {{ title }}
        </span>
        <ul :aria-labelledby="dropdownId" class="dropdown-menu dropdown-menu-dark">
            <slot/>
        </ul>
    </li>
</template>
