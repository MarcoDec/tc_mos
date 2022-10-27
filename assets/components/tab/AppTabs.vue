<script setup>
    import {computed, onUnmounted, ref} from 'vue'
    import AppTabLink from './AppTabLink.vue'
    import useTabs from '../../stores/tab/tabs'

    const iconMode = ref(false)
    const props = defineProps({id: {required: true, type: String}})
    const css = computed(() => ({'icon-mode': iconMode.value}))
    const icon = computed(() => `${props.id}-icon`)
    const tabs = useTabs(props.id)

    onUnmounted(() => {
        tabs.dispose()
    })
</script>

<template>
    <div :id="id" :class="css" class="d-flex">
        <ul class="bg-white nav nav-tabs" role="tablist">
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
