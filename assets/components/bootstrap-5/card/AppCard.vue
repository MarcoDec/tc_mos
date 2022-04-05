<script setup>
    import {computed} from 'vue'

    const props = defineProps({
        bgVariant: {default: null, type: String},
        title: {required: true, type: String},
        titleTag: {default: 'h2', type: String}
    })
    const bg = computed(() => (props.bgVariant === null ? null : `bg-${props.bgVariant}`))
</script>

<template>
    <div :class="bg" class="card">
        <component :is="titleTag" class="card-title">
            {{ title }}
        </component>
        <div class="card-body">
            <slot/>
        </div>
    </div>
</template>
