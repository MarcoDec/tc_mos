<script lang="ts" setup>
    import {defineProps} from 'vue'

    defineProps<{cssClass?: string}>()
</script>

<template>
    <div :class="cssClass" class="card">
        <div class="card-body">
            <slot/>
        </div>
    </div>
</template>
