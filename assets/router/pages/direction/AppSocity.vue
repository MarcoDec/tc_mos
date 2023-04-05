<script setup>
import AppSocityListTable from './AppSocityListTable.vue';
import AppSuspense from '../../../components/AppSuspense.vue';

    defineProps({
        fields: {default: () => [], type: Array},
        icon: {required: true, type: String},
        title: {required: true, type: String}
    })
</script>

<template>
    <AppSuspense>
        <AppSocityListTable :fields="fields" :icon="icon" :title="title"/>
    </AppSuspense>
</template>


