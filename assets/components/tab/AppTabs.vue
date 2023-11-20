<script setup>
    import {computed, onUnmounted, ref, onMounted, toRefs} from 'vue'
    import AppTabLink from './AppTabLink.vue'
    import useTabs from '../../stores/tab/tabs'

    const {id, iconMode, formatNav} = toRefs(defineProps({
        id: {required: true, type: String},
        iconMode: {required: false, type: Boolean},
        formatNav: {required: false, type: String, default: 'flex'}
    }))
    const localIconMode = ref(iconMode.value)
    const css = computed(() => ({/*'icon-mode': iconMode.value,*/ 'flex-row': formatNav.value !== 'flex'}))
    const cssLi = computed(() => ({'nav-item': formatNav.value === 'flex', 'nav-link-horizontal': formatNav.value !== 'flex'}))
    const icon = computed(() => `${id.value}-icon`)
    const tabs = useTabs(id.value)

    onUnmounted(() => {
        tabs.dispose()
    })

    onMounted(() => {
        //document.documentElement.style.setProperty(`--form-${props.id}`, props.formatNav)
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
                    <input :id="icon" v-model="localIconMode" class="form-check-input" type="checkbox"/>
                </div>
            </li>
            <AppTabLink v-for="tab in tabs.tabs" :key="tab.id" :icon-mode="localIconMode" :tab="tab" :format-nav="formatNav"/>
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
