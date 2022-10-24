<script setup>
    import {computed} from 'vue'

    const props = defineProps({icon: {type: Boolean}, tab: {required: true, type: Object}})
    const css = computed(() => ({active: props.tab.active}))
</script>

<template>
    <li :title="tab.title" class="nav-item" role="presentation">
        <button
            :id="tab.labelledby"
            :aria-controls="tab.id"
            :class="css"
            :data-bs-target="tab.target"
            class="nav-link"
            data-bs-toggle="tab"
            role="button"
            type="button">
            <Fa :icon="tab.icon" class="me-1"/>
            <template v-if="!icon">
                {{ tab.title }}
            </template>
        </button>
    </li>
</template>
