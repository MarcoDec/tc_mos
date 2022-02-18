<script lang="ts" setup>
    import {computed, defineProps, inject} from 'vue'
    import {useNamespacedGetters, useNamespacedState} from 'vuex-composition-helpers'
    import type {Ref} from 'vue'

    const props = defineProps<{id: string}>()
    const cardId = computed(() => `${props.id}-card`)
    const modulePath = inject<string>('modulePath')
    const parentModuleName = useNamespacedState(modulePath, ['parentModuleName']).parentModuleName as Ref<string>
    const selected = useNamespacedGetters(parentModuleName.value, ['selected']).selected as Ref<boolean>
    const formTag = computed(() => (selected.value ? 'AppTreeFormItem' : 'AppTreeForm'))
</script>

<template>
    <AppRow :id="id">
        <AppTree :module-path="modulePath" class="col"/>
        <component :is="formTag" :id="cardId" :selected="selected" class="col"/>
    </AppRow>
</template>
