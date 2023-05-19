<script setup>
    import {computed, defineEmits, defineProps} from 'vue'
    import AppFormField from './field/AppFormField.vue'
    const emit = defineEmits(['update:modelValue', 'submit'])
    const props = defineProps({
        fields: {required: true, type: Object},
        id: {required: true, type: String}
        // modelValue: {default: null, type: Object}
    })
    console.log('props', props)
    const tabs = computed(() => {
        for (const field of props.fields) if (field.mode === 'tab') return true
        return false
    })
    console.log('tabs', tabs)
    function input(value) {
        emit('update:modelValue', value)
    }
</script>

<template>
    <form :id="id" autocomplete="off" novalidate>
        <AppTabs v-if="tabs" id="gui-start" class="gui-start-content">
            <AppFormField
                v-for="field in fields"
                :key="field.name"
                :field="field"
                :form="id"
                @input="input">
                <slot :name="field.name"/>
            </AppFormField>
        </AppTabs>

        <template v-else>
            <AppFormField
                v-for="field in fields"
                :key="field.name"
                :field="field"
                :form="id"
                @input="input"/>
        </template>
    </form>
</template>

<style scoped>
fieldset.scheduler-border {
  border: 1px groove #ddd !important;
  padding: 0 1.4em 1.4em 1.4em !important;
  margin: 0 0 1.5em 0 !important;
  -webkit-box-shadow: 0px 0px 0px 0px #000;
  box-shadow: 0px 0px 0px 0px #000;
}

legend.scheduler-border {
  font-size: 1.2em !important;
  font-weight: bold !important;
  text-align: left !important;
  width: auto;
  padding: 0 10px;
  border-bottom: none;
}
</style>
