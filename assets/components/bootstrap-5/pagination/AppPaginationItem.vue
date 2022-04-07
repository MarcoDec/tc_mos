<script setup>
    import {computed} from 'vue'

    const props = defineProps({current: {required: false, type: Boolean}})
    const li = computed(() => ({active: props.current, 'page-item': true}))
    const liProps = computed(() => (props.current ? {'aria-current': 'page', class: li.value} : {class: li.value}))
</script>

<template>
    <li v-bind="liProps">
        <span class="page-link">
            <slot/>
        </span>
    </li>
</template>
