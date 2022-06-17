<script lang="ts" setup>
    import {defineProps} from 'vue'

    defineProps<{icon: string, to: string}>()
</script>

<template>
    <li>
        <AppRouterLink :to="to" class="dropdown-item">
            <Fa :icon="icon"/>
            <slot/>
        </AppRouterLink>
    </li>
</template>
