<script setup>
    import useTabs from '../../stores/tab/tabs'

    const props = defineProps({
        active: {type: Boolean},
        icon: {required: true, type: String},
        id: {required: true, type: String},
        tabs: {required: true, type: String},
        title: {required: true, type: String}
    })
    const tab = useTabs(props.tabs).push({active: props.active, icon: props.icon, id: props.id, title: props.title})
    // console.log('tab', tab)
</script>

<template>
    <div :id="tab.id" :aria-labelledby="tab.labelledby" :class="tab.css" class="fade tab-pane" role="tabpanel">
        <slot/>
    </div>
</template>

<style scoped>
div.active { position: relative; z-index: 0; overflow: scroll; max-height: 100%}
.gui-start-content {
    font-size: 14px;
}
</style>
