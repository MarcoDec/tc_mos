<script setup>
    import {onMounted, onUnmounted} from 'vue'
    import AppTree from '../../components/tree/AppTree'
    import generateFamilies from '../../stores/family/families'
    import {useMachine} from '../../machine'
    import {useRoute} from 'vue-router'

    defineProps({fields: {required: true, type: Object}, label: {required: true, type: String}})

    const route = useRoute()
    const families = generateFamilies(route.name)
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
                Familles de {{ label }}
            </h1>
        </div>
        <AppTree :id="tree" :families="families" :fields="fields" :items="families.roots" :machine="machine"/>
    </AppOverlay>
</template>
