<script lang="ts" setup>
    import type {State as RootState} from '../store'
    import {useState} from 'vuex-composition-helpers'

    const spinner = useState<RootState>(['spinner']).spinner
</script>

<template>
    <AppModalError id="modal-error"/>
    <AppTopNavbar/>
    <AppContainer>
        <RouterView/>
    </AppContainer>
    <div v-if="spinner" class="overlay">
        <div class="position-relative spinner-border start-50 top-50" role="status"/>
    </div>
</template>
