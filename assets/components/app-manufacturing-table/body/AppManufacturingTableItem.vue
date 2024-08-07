<script  setup>
    import {defineProps, ref} from 'vue'
    const emits = defineEmits(['update:modelValue'])
    const props = defineProps({
        fields: {required: true, type: Array},
        form: {required: true, type: String},
        id: {required: true, type: String},
        item: {required: true, type: Object},
        title: {required: true, type: String}
    })
    // console.log('props', props)
    const localItem = ref(null)
    localItem.value = props.item
    function onModelValueUpdated(fieldName, value) {
        // console.log('onModelValueUpdated', fieldName, value)
        //On positionnne la valeur dans l'objet localItem pour la propriété fieldName
        localItem.value[fieldName] = value
        //On émet l'événement update:modelValue avec la nouvelle valeur
        emits('update:modelValue', localItem.value)
    }
    // function getComponentType(field) {
    //     if (field.name === 'quantite' && title === 'collapse new Ofs') {
    //         return 'AppManufacturingTableItemQuantite'
    //     }
    //     if (field.name === 'siteDeProduction' && title === 'collapse new Ofs') {
    //         return 'AppManufacturingTableItemSiteDeProduction'
    //     }
    //     return 'AppManufacturingTableItemField'
    // }
</script>

<template>
    <tr>
        <component
            :is="field.name === 'quantite' && title === 'collapse new Ofs' ? 'AppManufacturingTableItemQuantite' : 'AppManufacturingTableItemField'"
            v-for="field in fields"
            :id="id"
            :key="field.name"
            :field="field"
            :item="item"
            :title="title"
            :form="form"
            @update:model-value="(value) => onModelValueUpdated(field.name, value)"/>
    </tr>
</template>
