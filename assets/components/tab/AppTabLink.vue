<script setup>
    import {computed, defineProps} from 'vue'
    const props = defineProps(
        {
            iconMode: {required: false, type: Boolean/*, default: false*/},
            tab: {required: true, type: Object},
            formatNav: {required: false, type: String, default: 'flex'}
        }
    )
    const cssLi = computed(() => ({'nav-item': props.formatNav === 'flex', 'nav-link-horizontal': props.formatNav !== 'flex'}))
    const cssDivText = computed(() => ({'nav-link-text-horizontal': props.formatNav === 'flex', 'nav-link-text': props.formatNav !== 'flex'}))
</script>

<template>
    <li :title="tab.title" :class="cssLi" role="presentation">
        <button
            :id="tab.labelledby"
            :aria-controls="tab.id"
            :aria-selected="tab.active"
            :class="tab.activeCss"
            :data-bs-target="tab.target"
            class="font-xsmall nav-link"
            data-bs-toggle="tab"
            role="tab"
            type="button">
            <Fa :icon="tab.icon" class="me-1"/>
            <div v-if="!iconMode" :class="cssDivText">
                {{ tab.title }}
            </div>
        </button>
    </li>
</template>

<style scoped>
    button {
        border-radius: 10px;
        border: 1px solid grey;
        box-shadow: black 3px 3px 3px;
        margin-right: 5px;
    }
    button:hover {
        box-shadow: black 3px 3px 10px;
        background-color: #43abd7 !important;
    }
    button.active {
        background-color: #43abd7 !important;
        color: white !important;
        box-shadow: inset black 0px 0px 10px;
    }
    div.nav-link-text {
        text-wrap: normal;
        max-width: 120px;
        width: 120px
    }
    div.nav-link-text-horizontal {
        text-wrap: normal;
    }
    .nav-link-horizontal {
        max-width: 150px;
        margin: 0px;
    }
</style>
