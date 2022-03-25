<script lang="ts" setup>
    import {computed, defineProps} from 'vue'
    import FontAwesomeIcon from '@fortawesome/vue-fontawesome/src/components/FontAwesomeIcon'

    const props = defineProps({brands: {required: false, type: Boolean}, icon: {required: true, type: String}})
    const normalizedIcon = computed(() => (props.brands ? ['fab', props.icon] : props.icon))
</script>

<template>
    <FontAwesomeIcon :icon="normalizedIcon"/>
</template>
