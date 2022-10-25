<script setup>
    import AppTabLink from './AppTabLink.vue'
    import {onUnmounted} from 'vue'
    import useTabs from '../../stores/tab/tabs'

    const props = defineProps({id: {required: true, type: String}})
    const tabs = useTabs(props.id)

    onUnmounted(() => {
        tabs.dispose()
    })
</script>

<template>
    <div :id="id">
        <ul class="bg-white nav nav-tabs" role="tablist">
            <AppTabLink v-for="tab in tabs.tabs" :key="tab.id" :tab="tab"/>
        </ul>
        <div class="bg-white tab-content">
            <slot/>
        </div>
    </div>
</template>
