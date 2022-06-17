<script lang="ts" setup>
    import {defineEmits, defineProps} from 'vue'
    import type {PropType} from 'vue'
    import type {Tab} from '../../../types/bootstrap-5'

    const emit = defineEmits<(e: 'click', name: string) => void>()
    const props = defineProps({
        icon: {required: false, type: Boolean},
        tab: {required: true, type: Object as PropType<Tab>}
    })
    function click(): void {
        emit('click', props.tab.id)
    }
</script>

<template>
    <li class="nav-item" role="presentation">
        <button
            :id="tab.labelledby"
            :aria-controls="tab.id"
            :class="tab.active"
            :data-bs-target="tab.target"
            class="nav-link"
            data-bs-toggle="tab"
            role="tab"
            type="button"
            @click="click">
            <Fa :icon="tab.icon"/>
            <template v-if="!icon">
                {{ tab.title }}
            </template>
        </button>
    </li>
</template>
