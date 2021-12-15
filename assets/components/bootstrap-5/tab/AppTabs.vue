<script lang="ts" setup>
    import {onMounted, onUnmounted, provide, ref, watch} from 'vue'
    import {Tab as BTab} from 'bootstrap'
    import type {Tab} from '../../../types/bootstrap-5'

    const el = ref<HTMLUListElement>()
    const bTab = ref<BTab | null>(null)
    const tabs = ref<Tab[]>([])

    function dispose(): void {
        if (bTab.value !== null) {
            bTab.value.dispose()
            bTab.value = null
        }
    }

    function instantiate(): void {
        if (typeof el.value === 'undefined')
            return

        dispose()
        bTab.value = new BTab(el.value)
    }

    provide('tabs', tabs)

    onMounted(() => {
        instantiate()
    })

    onUnmounted(() => {
        dispose()
    })

    watch(tabs, () => {
        instantiate()
    })
</script>

<template>
    <div>
        <ul ref="el" class="bg-white h-10 nav nav-tabs" role="tablist">
            <li v-for="tab in tabs" :key="tab.labelledby" class="nav-item" role="presentation">
                <button
                    :id="tab.labelledby"
                    :aria-controls="tab.id"
                    :class="tab.active"
                    :data-bs-target="tab.target"
                    class="nav-link"
                    data-bs-toggle="tab"
                    role="tab"
                    type="button">
                    {{ tab.title }}
                </button>
            </li>
        </ul>
        <div class="bg-white h-90 tab-content">
            <slot/>
        </div>
    </div>
</template>
