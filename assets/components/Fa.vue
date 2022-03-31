<script setup>
    import FontAwesomeIcon from '@fortawesome/vue-fontawesome/src/components/FontAwesomeIcon'
    import {computed} from 'vue'

    const props = defineProps({brands: {required: false, type: Boolean}, icon: {required: true, type: String}})
    const normalizedIcon = computed(() => (props.brands ? ['fab', props.icon] : props.icon))
</script>

<template>
    <FontAwesomeIcon :icon="normalizedIcon"/>
</template>
