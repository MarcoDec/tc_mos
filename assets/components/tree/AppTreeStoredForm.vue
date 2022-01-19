<script lang="ts" setup>
    import {computed, defineProps} from 'vue'
    import type {Getters} from '../../store/purchase/component/families'
    import {useNamespacedGetters} from 'vuex-composition-helpers'

    const props = defineProps<{family: number}>()
    const findFamily = useNamespacedGetters<Getters>('families', ['find']).find
    const state = computed(() => findFamily.value(props.family.toString()))
</script>

<template>
    <AppTreeForm :values="state"/>
</template>
