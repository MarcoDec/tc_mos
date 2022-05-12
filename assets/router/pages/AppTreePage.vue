<script setup>
    import {onMounted, onUnmounted} from 'vue'
    import AppTree from '../../components/tree/AppTree'
    import {createMachine} from 'xstate'
    import useFamiliesStore from '../../stores/purchase/component/family/families'
    import {useMachine} from '@xstate/vue'
    import {useRoute} from 'vue-router'

    const families = useFamiliesStore()
    const route = useRoute()
    const {send, state} = useMachine(createMachine({
        id: route.name,
        initial: 'loading',
        states: {
            display: {type: 'final'},
            loading: {on: {success: {target: 'display'}}}
        }
    }))

    onMounted(async () => {
        await families.fetch()
        send('success')
    })

    onUnmounted(() => {
        families.dispose()
    })
</script>

<template>
    <AppOverlay :id="route.name" :spinner="state.matches('loading')" class="row">
        <div class="row">
            <h1 class="col">
                <Fa icon="layer-group"/>
                Familles de composants
            </h1>
        </div>
        <AppTree :items="families.roots"/>
    </AppOverlay>
</template>
