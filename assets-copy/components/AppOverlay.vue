<script setup>
    defineProps({loading: {type: Boolean}})
</script>

<template>
    <div>
        <div v-if="loading" class="overlay">
            <div class="position-relative spinner-border start-50 top-50" role="status"/>
        </div>
        <slot/>
    </div>
</template>
