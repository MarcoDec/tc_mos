<script lang="ts" setup>
    import {computed, defineProps, inject, onMounted} from 'vue'
    import type {Ref} from 'vue'
    import type {Tab} from '../../../types/bootstrap-5'

    const tabs = inject<Ref<Tab[]>>('tabs')
    const props = defineProps({
        active: {required: false, type: Boolean},
        id: {required: true, type: String},
        title: {required: true, type: String}
    })
    const activeClass = computed(() => ({active: props.active}))
    const labelledby = computed(() => `${props.id}-tab`)
    const target = computed(() => `#${props.id}`)

    onMounted(() => {
        if (typeof tabs !== 'undefined') {
            tabs.value.push({
                active: activeClass.value,
                id: props.id,
                labelledby: labelledby.value,
                target: target.value,
                title: props.title
            })
        }
    })
</script>

<template>
    <div :id="id" :aria-labelledby="labelledby" :class="activeClass" class="tab-pane" role="tabpanel">
        <slot/>
    </div>
</template>
