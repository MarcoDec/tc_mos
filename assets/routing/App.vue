<script lang="ts" setup>
    import type {State as RootState} from '../store'
    import {useState} from 'vuex-composition-helpers'

    const spinner = useState<RootState>(['spinner']).spinner
</script>

<template>
    <AppTopNavbar/>
    <AppContainer>
        <RouterView/>
    </AppContainer>
    <div v-if="spinner" class="overlay">
        <div class="spinner spinner-border spinner-border-sm"/>
    </div>
</template>

<style scoped>
    .overlay {
        background-color: #EFEFEF;
        position: fixed;
        width: 100%;
        height: 100%;
        z-index: 1000;
        top: 0;
        left: 0;
        opacity: .5;
        filter: alpha(opacity=50);
    }

    .spinner {
        display: block;
        position: fixed;
        z-index: 1031;
        top: 50%;
        right: 50%;
    }
</style>
