<script lang="ts" setup>
    import type {FormField, FormValue, FormValues} from '../../../types/bootstrap-5'
    import {defineEmits, defineProps, ref, withDefaults} from 'vue'
    import clone from 'clone'

    const emit = defineEmits<{(e: 'update:values', values: FormValues): void, (e: 'submit'): void}>()
    const props = withDefaults(
        defineProps<{fields: FormField[], values?: FormValues}>(),
        {values: () => ({})}
    )
    const disabled = ref(true)

    function input({name, value}: {value: FormValue, name: string}): void {
        const cloned = clone(props.values)
        cloned[name] = value
        emit('update:values', cloned)
    }
    function handleSubmit() {
      emit('submit')
     /* if (disabled.value) {

        console.log('disabled true')
      }
      disabled.value = true*/
    }

</script>


<template>
    <form autocomplete="off" @submit.prevent="handleSubmit">
        <AppFormGroup
            v-for="field in fields"
            :key="field.name"
            :field="field"
            :value="values[field.name]"
            @input="input"/>
        <AppBtn class="float-end" type="submit">
            Connexion
        </AppBtn>
    </form>
</template>

<style lang="scss" scoped>
.float-end.disabled{
  pointer-events: none;
  cursor: default;
}

</style>