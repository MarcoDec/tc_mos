<script setup>
    import {computed, onUnmounted, ref, onMounted} from 'vue'
    import AppTabLink from './AppTabLink.vue'
    import useTabs from '../../stores/tab/tabs'

    const iconMode = ref(false)
    const props = defineProps({
        id: {required: true, type: String},
        formatNav: {required: true, type: String, default: 'flex'}
    })
    const css = computed(() => ({'icon-mode': iconMode.value}))
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
        <ul class="bg-white nav nav-tabs" role="tablist" :style="`--form: var(--form-${id})`">
            <li class="nav-item tab-icon" role="presentation" title="Icônes">
                <label :for="icon" class="form-check-label">
                    <Fa icon="icons"/>
                </label>
                <div class="form-check form-switch">
                    <input :id="icon" v-model="iconMode" class="form-check-input" type="checkbox"/>
                </div>
            </li>
            <AppTabLink v-for="tab in tabs.tabs" :key="tab.id" :tab="tab"/>
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

    .nav-tabs {
         display: var(--form);
    }
    .tab-content{
        width: 100%;
    }

    li {
        padding-bottom: 20px;
    }
</style>
