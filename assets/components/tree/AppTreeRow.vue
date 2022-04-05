<script lang="ts" setup>
    import {computed, defineProps, inject} from 'vue'
    import type {Getters} from '../../store/tree'
    import {useNamespacedGetters} from 'vuex-composition-helpers'

    const firstItem = inject('firstItem', '')
    const moduleName = inject('moduleName', '')
    const props = defineProps<{id: string}>()
    const cardId = computed(() => `${props.id}-card`)
    const selected = useNamespacedGetters<Getters>(moduleName, ['selected']).selected
    const formTag = computed(() => (selected.value === null ? 'AppTreeForm' : 'AppTreeFormItem'))
</script>

<template>
    <AppRow :id="id">
        <AppTree :module-path="firstItem" class="col"/>
        <component :is="formTag" :id="cardId" :selected="selected" class="col"/>
    </AppRow>
</template>
