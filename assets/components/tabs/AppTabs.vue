<script setup>
    import {computed, onMounted, onUnmounted, provide, ref, watch} from 'vue'
    import AppTabBtn from './AppTabBtn'
    import {Tab as BTab} from 'bootstrap'

    const props = defineProps({
        icon: {type: Boolean},
        iconSwitch: {type: Boolean},
        id: {required: false, type: String},
        vertical: {type: Boolean}
    })
    const bTab = ref(null)
    const divFlex = computed(() => `flex-${props.vertical ? 'row' : 'column'}`)
    const el = ref()
    const iconMode = ref(false)
    const iconSwitchId = computed(() => `${props.id}-icon-switch`)
    const tabs = ref([])
    const tabsSize = computed(() => `${props.vertical ? 'w' : 'h'}-90`)
    const ulCss = computed(() => `flex-${props.vertical ? 'column' : 'row'} ${props.vertical ? 'w' : 'h'}-10`)

    function dispose() {
        if (bTab.value !== null) {
            bTab.value.dispose()
            bTab.value = null
        }
    }

    function instantiate() {
        if (typeof el.value === 'undefined')
            return
        dispose()
        bTab.value = new BTab(el.value)
    }

    provide('tabs', tabs)

    onMounted(instantiate)
    onUnmounted(dispose)

    watch(tabs, instantiate)

    watch(() => props.icon, icon => {
        iconMode.value = icon
    })
</script>

<template>
    <div :id="id" :class="divFlex" class="d-flex">
        <ul ref="el" :class="ulCss" class="bg-white d-flex nav nav-tabs" role="tablist">
            <li v-show="iconSwitch" class="form-check form-switch nav-item" role="presentation">
                <input :id="iconSwitchId" v-model="iconMode" class="form-check-input" type="checkbox"/>
                <label :for="iconSwitchId" class="form-check-label">
                    <Fa icon="icons"/>
                    <template v-if="!iconMode">
                        Icône
                    </template>
                </label>
            </li>
            <AppTabBtn v-for="tab in tabs" :key="tab.labelledby" :icon="iconMode" :tab="tab"/>
        </ul>
        <div :class="tabsSize" class="bg-white overflow-hidden tab-content">
            <slot/>
        </div>
    </div>
</template>
