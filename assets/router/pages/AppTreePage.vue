<script setup>
    import {assign, createMachine} from 'xstate'
    import {onMounted, onUnmounted} from 'vue'
    import AppTree from '../../components/tree/AppTree'
    import useFamiliesStore from '../../stores/purchase/component/family/families'
    import {useMachine} from '@xstate/vue'
    import {useRoute} from 'vue-router'

    const families = useFamiliesStore()
    const route = useRoute()
    const machine = useMachine(createMachine({
        context: {violations: []},
        id: route.name,
        initial: 'loading',
        states: {
            error: {on: {submit: {actions: [assign({violations: []})], target: 'loading'}}},
            form: {on: {submit: {actions: [assign({violations: []})], target: 'loading'}}},
            loading: {
                on: {
                    fail: {actions: [assign((context, {violations}) => ({violations}))], target: 'error'},
                    success: {target: 'form'}
                }
            }
        }
    }))
    const tree = `${route.name}-tree`

    onMounted(async () => {
        await families.fetch()
        machine.send('success')
    })

    onUnmounted(() => {
        families.dispose()
    })
</script>

<template>
    <AppOverlay :id="route.name" :spinner="machine.state.value.matches('loading')">
        <div class="row">
            <h1 class="col">
                <Fa icon="layer-group"/>
                Familles de composants
            </h1>
        </div>
        <AppTree :id="tree" :items="families.roots" :machine="machine"/>
    </AppOverlay>
</template>
