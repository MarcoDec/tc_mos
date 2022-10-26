<script setup>
    import {computed} from 'vue'

    const props = defineProps({id: {required: true, type: String}})
    const dropdownId = computed(() => `${props.id}-dropdown`)
</script>

<template>
    <div :id="id" class="dropdown">
        <slot :id="dropdownId" name="toggle"/>
        <ul :aria-labelledby="dropdownId" class="dropdown-menu dropdown-menu-dark dropdown-menu-end">
            <slot/>
        </ul>
    </div>
</template>
