<script setup>
    import {computed} from 'vue'

    const props = defineProps({
        icon: {required: true, type: String},
        id: {required: true, type: String},
        title: {required: true, type: String}
    })
    const dropdownId = computed(() => `${props.id}-dropdown`)
</script>

<template>
    <li :id="id" class="dropdown nav-item">
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
