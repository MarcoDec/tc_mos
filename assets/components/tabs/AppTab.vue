<script setup>
    import {computed, inject, onMounted, onUnmounted} from 'vue'

    const tabs = inject('tabs')
    const props = defineProps({
        active: {type: Boolean},
        icon: {required: true, type: String},
        id: {required: true, type: String},
        title: {required: true, type: String}
    })
    const activeClass = computed(() => ({active: props.active}))
    const labelledby = computed(() => `${props.id}-tab`)
    const target = computed(() => `#${props.id}`)

    onMounted(() => {
        if (typeof tabs !== 'undefined')
            tabs.value.push({
                active: props.active,
                icon: props.icon,
                id: props.id,
                labelledby: labelledby.value,
                target: target.value,
                title: props.title
            })
    })

    onUnmounted(() => {
        if (typeof tabs !== 'undefined')
            tabs.value = tabs.value.filter(({id}) => id !== props.id)
    })
</script>

<template>
    <div
        :id="id"
        :aria-labelledby="labelledby"
        :class="activeClass"
        class="h-100 overflow-auto p-2 tab-pane"
        role="tabpanel">
        <slot/>
    </div>
</template>

<style scoped>
div.active { position: relative; z-index: 0; overflow: scroll; max-height: 100%}
.gui-start-content {
    font-size: 14px;
}
</style>
