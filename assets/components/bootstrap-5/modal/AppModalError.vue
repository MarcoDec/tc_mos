<script lang="ts" setup>
    import {defineProps} from 'vue'

    defineProps<{code: string}>()
</script>

<template>
    <AppModal title="Erreur">
        <AppAlert>
            <AppBadge>Erreur&nbsp;{{ code }}</AppBadge>
        </AppAlert>
    </AppModal>
</template>
