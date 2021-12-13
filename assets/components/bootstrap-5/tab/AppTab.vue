<script lang="ts" setup>
    import {computed, defineProps} from 'vue'

    const props = defineProps<{tabs: string, title: string}>()
    const nav = computed(() => `nav-${props.tabs}`)
</script>

<template>
    <Teleport :to="nav">
        <li class="nav-item" role="presentation">
            <button class="nav-link" role="tab" type="button">
                {{ title }}
            </button>
        </li>
    </Teleport>

    <div class="tab-pane">
        <slot/>
    </div>
</template>
