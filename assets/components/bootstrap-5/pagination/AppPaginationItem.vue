<script setup>
    import {computed} from 'vue'

    const emit = defineEmits(['click'])
    const props = defineProps({current: {required: false, type: Boolean}, page: {required: true, type: Number}})
    const li = computed(() => ({active: props.current, 'page-item': true}))
    const liProps = computed(() => (props.current ? {'aria-current': 'page', class: li.value} : {class: li.value}))

    function click() {
        emit('click', props.page)
    }
</script>

<template>
    <li v-bind="liProps" @click="click">
        <span class="page-link">
            <slot/>
        </span>
    </li>
</template>
