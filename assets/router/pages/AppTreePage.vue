<script setup>
    import {onMounted, onUnmounted} from 'vue'
    import AppTree from '../../components/tree/AppTree'
    import useFamiliesStore from '../../stores/purchase/component/family/families'
    import {useMachine} from '../../machine'
    import {useRoute} from 'vue-router'

    const families = useFamiliesStore()
    const route = useRoute()
    const machine = useMachine(route.name)
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
