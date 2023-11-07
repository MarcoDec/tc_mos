<script setup>
    import {computed, onUnmounted, ref, onMounted} from 'vue'
    import AppTabLink from './AppTabLink.vue'
    import useTabs from '../../stores/tab/tabs'

    const iconMode = ref(false)
    const props = defineProps({
        id: {required: true, type: String},
        formatNav: {required: false, type: String, default: 'flex'}
    })
    const css = computed(() => ({'icon-mode': iconMode.value, 'flex-row': props.formatNav !== 'flex'}))
    const cssLi = computed(() => ({'nav-item': props.formatNav === 'flex', 'nav-link-horizontal': props.formatNav !== 'flex'}))
    const icon = computed(() => `${props.id}-icon`)
    const tabs = useTabs(props.id)

    onUnmounted(() => {
        tabs.dispose()
    })

    onMounted(() => {
        document.documentElement.style.setProperty(`--form-${props.id}`, props.formatNav)
    })
</script>

<template>
    <div :id="id" :class="css" class="d-flex">
        <ul class="bg-white nav" :class="{'nav-tabs-flex': formatNav === 'flex', 'nav-tabs-block': formatNav !== 'flex'}" role="tablist" :style="`--form: var(--form-${id})`">
            <li :class="cssLi" class="tab-icon" role="presentation" title="Icônes">
                <label :for="icon" class="form-check-label">
                    <Fa icon="icons"/>
                </label>
                <div class="form-check form-switch">
                    <input :id="icon" v-model="iconMode" class="form-check-input" type="checkbox"/>
                </div>
            </li>
            <AppTabLink v-for="tab in tabs.tabs" :key="tab.id" :tab="tab" :format-nav="formatNav"/>
        </ul>
        <div class="bg-white tab-content">
            <slot/>
        </div>
    </div>
</template>

<style scoped>
    #gui-start-bottom {
        flex-direction: column;
        width: 100vw;
    }

    .nav-tabs-flex {
        display: flex;
        margin-top: 0px;
    }
    .nav-tabs-block {
        display: flex;
        flex-direction: column;
        margin: 0px;
    }
    .tab-content{
        width: 100%;
    }

    li {
        padding-bottom: 0px;
    }

    .nav-link-horizontal {
        max-width: 150px;
    }
</style>
