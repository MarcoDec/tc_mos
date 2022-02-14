<script lang="ts" setup>
    import {computed, defineProps, inject} from 'vue'

    const props = defineProps<{id: string}>()
    const cardId = computed(() => `${props.id}-card`)
    const modulePath = inject<string>('modulePath')
</script>

<template>
    <AppRow :id="id">
        <AppTree :module-path="modulePath" class="col"/>
        <AppTreeForm :id="cardId" class="col"/>
    </AppRow>
</template>
