<script lang="ts" setup>
    import type {PropType} from 'vue'
    import type {Tab} from '../../../types/bootstrap-5'
    import {defineProps} from 'vue'

    defineProps({
        icon: {required: false, type: Boolean},
        tab: {required: true, type: Object as PropType<Tab>}
    })
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
            type="button">
            <Fa :icon="tab.icon"/>
            <template v-if="!icon">
                {{ tab.title }}
            </template>
        </button>
    </li>
</template>
