<script setup>
    defineProps({tab: {required: true, type: Object}})
</script>

<template>
    <li :title="tab.title" class="nav-item" role="presentation">
        <button
            :id="tab.labelledby"
            :aria-controls="tab.id"
            :aria-selected="tab.active"
            :class="tab.activeCss"
            :data-bs-target="tab.target"
            class="nav-link"
            data-bs-toggle="tab"
            role="tab"
            type="button">
            <Fa :icon="tab.icon" class="me-1"/>
            <span class="nav-link-text">{{ tab.title }}</span>
        </button>
    </li>
</template>
