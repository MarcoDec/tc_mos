<script lang="ts" setup>
    import {computed, defineProps, onMounted, onUnmounted, ref} from 'vue'
    import {Dropdown} from 'bootstrap'

    const props = defineProps<{icon: string, id: string, title: string}>()
    const dropdown = ref<Dropdown | null>(null)
    const dropdownId = computed(() => `${props.id}-dropdown`)
    const el = ref<HTMLLIElement>()

    function dispose(): void {
        if (dropdown.value !== null) {
            dropdown.value.dispose()
            dropdown.value = null
        }
    }

    function instantiate(): void {
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
