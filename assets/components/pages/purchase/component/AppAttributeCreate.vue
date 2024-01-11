<script setup>
    import AppFormJS from '../../../form/AppFormJS.js'
    import AppSuspense from '../../../AppSuspense.vue'
    import useAttributesStore from '../../../../stores/attribute/attributes'

    const emit = defineEmits(['dataAttribute'])
    const storeAttributes = useAttributesStore()
    await storeAttributes.getAttributes()

    defineProps({
        fieldsAttributs: {required: true, type: Array}
        // myBooleanFamily: {required: true, type: Boolean}
    })

    const formInput = {}
    let data = {}

    function inputAttribute(value) {
        const key = Object.keys(value)[0]
        if (Object.prototype.hasOwnProperty.call(formInput, key)) {
            if (typeof value[key] === 'object') {
                if (typeof value[key].value !== 'undefined') {
                    const inputValue = parseFloat(value[key].value)
                    formInput[key] = {...formInput[key], value: inputValue}
                }
                if (typeof value[key].code !== 'undefined') {
                    const inputCode = value[key].code
                    formInput[key] = {...formInput[key], code: inputCode}
                }
            } else {
                formInput[key] = value[key]
            }
        } else {
            formInput[key] = value[key]
        }
        data = {
            formInput
        }

        emit('dataAttribute', data)
        // if (props.myBooleanFamily===true) {
        //     formInput={}
        // }
    }
</script>

<template>
    <AppSuspense>
        <AppFormJS v-if="fieldsAttributs.length !== 0" id="addAttributes" :fields="fieldsAttributs" @update:model-value="inputAttribute"/>
        <p v-else class="bg-info text-white m-2">
            Si aucun attribut ne s'affiche c'est soit qu'aucune famille n'a été définie pour ce composant, soit qu'aucun attribut n'a été associé à la famille sélectionnées.
        </p>
    </AppSuspense>
</template>

