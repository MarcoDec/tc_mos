<script setup>
    import {computed, onMounted, onUnmounted} from 'vue'
    import AppTable from '../../components/table/AppTable'
    import {generateTableFields} from '../../components/validators'
    import {useMachine} from '../../machine'
    import {useRoute} from 'vue-router'

    const props = defineProps({
        fields: generateTableFields(),
        icon: {required: true, type: String},
        store: {required: true, type: Function},
        title: {required: true, type: String}
    })
    const route = useRoute()
    const machine = useMachine(route.name)
    const module = await props.store()
    const store = module['default']()
    const variant = computed(() => `text-${store.length > 0 ? 'dark' : 'white'}`)

    onMounted(async () => {
        await store.fetch()
        machine.send('success')
    })

    onUnmounted(() => {
        store.dispose()
    })
</script>

<template>
    <AppOverlay :id="route.name" :class="variant" :spinner="machine.state.value.matches('loading')">
        <div class="row">
            <h1 class="col">
                <Fa :icon="icon"/>
                <span class="ms-2">{{ title }}</span>
            </h1>
        </div>
        <AppTable :fields="fields" :items="store.items"/>
    </AppOverlay>
</template>
