<script lang="ts" setup>
    import {computed, defineProps, onMounted, onUnmounted, provide, ref, watch} from 'vue'
    import {Tab as BTab} from 'bootstrap'
    import type {Tab} from '../../../types/bootstrap-5'

    const props = defineProps({vertical: {required: false, type: Boolean}})

    const bTab = ref<BTab | null>(null)
    const el = ref<HTMLUListElement>()
    const divFlex = computed(() => `flex-${props.vertical ? 'row' : 'column'}`)
    const tabs = ref<Tab[]>([])
    const tabsSize = computed(() => `${props.vertical ? 'w' : 'h'}-90`)
    const ulCss = computed(() => `flex-${props.vertical ? 'column' : 'row'} ${props.vertical ? 'w' : 'h'}-10`)

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
    <div :class="divFlex" class="d-flex">
        <ul :class="ulCss" class="bg-white d-flex nav nav-tabs" role="tablist">
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
        <div :class="tabsSize" class="bg-white overflow-hidden tab-content">
            <slot/>
        </div>
    </div>
</template>
