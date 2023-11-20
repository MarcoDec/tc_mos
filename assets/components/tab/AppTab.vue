<script setup>
    import useTabs from '../../stores/tab/tabs'
    import {toRefs} from 'vue'

    const {active, icon, id, tabs, title} = toRefs(defineProps({
        active: {type: Boolean},
        icon: {required: true, type: String},
        id: {required: true, type: String},
        tabs: {required: true, type: String},
        title: {required: true, type: String}
    }))
    const tab = useTabs(tabs.value).push({active: active.value, icon: icon.value, id: id.value, title: title.value})
</script>

<template>
    <div :id="tab.id" :aria-labelledby="tab.labelledby" :class="tab.css" class="fade tab-pane" role="tabpanel">
        <slot/>
    </div>
</template>

<style scoped>
    div.tab-pane {
        box-shadow: inset black 10px 10px 10px -10px;
        padding: 5px 5px 5px 5px;
    }
</style>
