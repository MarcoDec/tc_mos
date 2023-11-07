<script setup>
    import {computed, defineProps} from 'vue'
    const props = defineProps({tab: {required: true, type: Object}, formatNav: {required: false, type: String, default: 'flex'}})
    const cssLi = computed(() => ({'nav-item': props.formatNav === 'flex', 'nav-link-horizontal': props.formatNav !== 'flex'}))
</script>

<template>
    <li :title="tab.title" :class="cssLi" role="presentation">
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
            <div class="nav-link-text">
                {{ tab.title }}
            </div>
        </button>
    </li>
</template>

<style scoped>
    button.active {
        background-color: #43abd7 !important;
        color: white !important;
    }
    div.nav-link-text {
        text-wrap: normal;
        max-width: 120px;
        width: 120px
    }
    .nav-link-horizontal {
        max-width: 150px;
    }
</style>
