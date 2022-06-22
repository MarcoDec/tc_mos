<script setup>
    import {onMounted, onUnmounted} from 'vue'
    import AppTree from '../../components/tree/AppTree'
    import generateFamilies from '../../stores/family/families'
    import {generateFields} from '../../components/props'
    import useAttributes from '../../stores/purchase/component/attributes'
    import {useMachine} from '../../machine'
    import {useRoute} from 'vue-router'

    const attributes = useAttributes()
    const props = defineProps({
        fields: generateFields(),
        label: {required: true, type: String},
        noDisplayAttr: {type: Boolean}
    })
    const route = useRoute()
    const families = generateFamilies(route.name)
    const machine = useMachine(route.name)
    const tree = `${route.name}-tree`

    onMounted(async () => {
        if (!props.noDisplayAttr)
            await attributes.fetch()
        await families.fetch()
        machine.send('success')
    })

    onUnmounted(() => {
        attributes.dispose()
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
        <AppTree
            :id="tree"
            :attributes="attributes"
            :families="families"
            :fields="fields"
            :items="families.roots"
            :machine="machine"
            :no-display-attr="noDisplayAttr"/>
    </AppOverlay>
</template>
