<script setup>
    import {computed, inject} from 'vue'
    import AppTree from './AppTree.vue'
    import AppTreeForm from './AppTreeForm.vue'
    import AppTreeFormItem from './AppTreeFormItem.vue'
    import {useNamespacedGetters} from 'vuex-composition-helpers'

    const firstItem = inject('firstItem', '')
    const moduleName = inject('moduleName', '')
    const props = defineProps({id: {required: true, type: String}})
    const cardId = computed(() => `${props.id}-card`)
    const selected = useNamespacedGetters(moduleName, ['selected']).selected
    const formTag = computed(() => (selected.value === null ? AppTreeForm : AppTreeFormItem))
</script>

<template>
    <AppRow :id="id">
        <AppTree :module-path="firstItem" class="col"/>
        <component :is="formTag" :id="cardId" :selected="selected" class="col"/>
    </AppRow>
</template>
