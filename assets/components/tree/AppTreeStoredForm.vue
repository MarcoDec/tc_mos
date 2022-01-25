<script lang="ts" setup>
    import {computed, defineEmits, defineProps} from 'vue'
    import type {Getters} from '../../store/purchase/component/families'
    import {useNamespacedGetters} from 'vuex-composition-helpers'

    const emit = defineEmits<(e: 'back') => void>()
    const props = defineProps<{family: number, id: string}>()
    const findFamily = useNamespacedGetters<Getters>('families', ['find']).find
    const state = computed(() => findFamily.value(props.family.toString()))

    function back(): void {
        emit('back')
    }
</script>

<template>
    <AppTreeForm :id="id" :values="state">
        <template #start>
            <AppBtn icon="backward" variant="danger" @click="back"/>
        </template>
        <AppBtn type="submit" variant="primary">
            <Fa icon="pencil-alt"/>
            Modifier
        </AppBtn>
    </AppTreeForm>
</template>
