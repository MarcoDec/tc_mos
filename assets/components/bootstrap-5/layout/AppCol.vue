<script setup>
    import {computed} from 'vue'
    import {colValidator as validator} from '../../../composition/bootstrap-5'

    const props = defineProps({
        cols: {default: null, type: Number, validator},
        tag: {default: 'div', type: String}
    })
    const colClass = computed(() => (props.cols === null ? 'col' : `col-${props.cols}`))
</script>

<template>
    <component :is="tag" :class="colClass">
        <slot/>
    </component>
</template>
