<script setup>
    import {ref} from 'vue'
    import useAttributesStore from '../../../stores/attribute/attributes'
    import AppFormJS from '../../../components/form/AppFormJS.js'
    import AppSuspense from '../../../components/AppSuspense.vue'

    const emit = defineEmits(['dataAttribute'])
    const storeAttributes = useAttributesStore()
    await storeAttributes.getAttributes()
    
    const props = defineProps({
        fieldsAttributs: {required: true, type:Array},
        myBooleanFamily: {required: true, type:Boolean}
    })

    let formInput= {}
    let data = {}
    

function inputAttribute(value) {
  const key = Object.keys(value)[0]  
  if (formInput.hasOwnProperty(key)) {
    if (typeof value[key] === 'object') {
      if (value[key].value !== undefined) { 
        const inputValue = parseFloat(value[key].value)
        formInput[key] = { ...formInput[key], value: inputValue }
      }
      if (value[key].code !== undefined) { 
        const inputCode = value[key].code
        formInput[key] = { ...formInput[key], code: inputCode }
      }
    }else{
        formInput[key] = value[key]
    }
  } else {
    formInput[key] = value[key];
  }
  console.log('formInput',formInput);
    data = {
        formInput: formInput,
    }
    if (props.myBooleanFamily===false) {
      console.log('ttttt');
      formInput={}
    }
    
  emit('dataAttribute', data);
}

</script>

<template>
    <AppSuspense>
       <AppFormJS id="addAttributes" v-if="fieldsAttributs.length !== 0" :fields="fieldsAttributs" @update:model-value="inputAttribute"/>
    </AppSuspense>
</template>

