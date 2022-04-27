<script setup>
    defineProps({icon: {type: Boolean}, tab: {required: true, type: Object}})
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
            <Fa :icon="tab.icon" class="me-1"/>
            <template v-if="!icon">
                {{ tab.title }}
            </template>
        </button>
    </li>
</template>
