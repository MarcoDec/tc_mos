<script setup>
    import {computed, onMounted, onUnmounted, ref} from 'vue'
    import {Dropdown} from 'bootstrap'

    const props = defineProps({
        icon: {required: true, type: String},
        id: {required: true, type: String},
        title: {required: true, type: String}
    })
    const dropdown = ref(null)
    const el = ref()
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
    <li :id="liId" ref="el" class="dropdown nav-item">
        <a
            :id="dropdownId"
            aria-expanded="false"
            class="dropdown-toggle nav-link"
            data-bs-toggle="dropdown"
            href="#"
            role="button">
            <Fa :icon="icon"/>
            {{ title }}
        </a>
        <ul :aria-labelledby="dropdownId" class="dropdown-menu dropdown-menu-dark">
            <slot/>
        </ul>
    </li>
</template>
