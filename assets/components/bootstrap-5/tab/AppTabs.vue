<script lang="ts" setup>
    import {computed, defineProps, onMounted, onUnmounted, provide, ref, watch} from 'vue'
    import {Tab as BTab} from 'bootstrap'
    import type {Tab} from '../../../types/bootstrap-5'

const emit = defineEmits<{
  (e: "click", name: string): void;
}>();
    const props = defineProps({
        iconSwitch: {required: false, type: Boolean},
        id: {required: true, type: String},
        vertical: {required: false, type: Boolean}
    })

    const bTab = ref<BTab | null>(null)
    const divFlex = computed(() => `flex-${props.vertical ? 'row' : 'column'}`)
    const el = ref<HTMLUListElement>()
    const iconMode = ref(false)
    const iconSwitchId = computed(() => `${props.id}-icon-switch`)
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
    function click(tab: string) {
  emit("click", tab );
}
</script>

<template>
    <div :id="id" :class="divFlex" class="d-flex">
        <ul :class="ulCss" class="bg-white d-flex nav nav-tabs" role="tablist">
            <li v-show="iconSwitch" class="form-check form-switch nav-item" role="presentation">
                <input :id="iconSwitchId" v-model="iconMode" class="form-check-input" type="checkbox"/>
                <label :for="iconSwitchId" class="form-check-label">
                    <Fa icon="icons"/>
                    <template v-if="!iconMode">
                        Icône
                    </template>
                </label>
            </li>
            <AppTabBtn v-for="tab in tabs" :key="tab.labelledby" :icon="iconMode" :tab="tab" @click="click"/>
        </ul>
        <div :class="tabsSize" class="bg-white overflow-hidden tab-content">
            <slot/>
        </div>
    </div>
</template>
