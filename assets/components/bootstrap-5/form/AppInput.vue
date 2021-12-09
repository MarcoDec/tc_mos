<script lang="ts" setup>
import type {BootstrapSize, FormField, FormValue} from '../../../types/bootstrap-5'
import {computed, defineEmits, defineProps, withDefaults} from 'vue'

const emit = defineEmits<(e: 'update:value', value: FormValue) => void>()
const props = withDefaults(
    defineProps<{ field: FormField, value?: FormValue, size?: BootstrapSize }>(),
    {size: 'sm', value: ''}
)

const sizeClass = computed(() => `form-control-${props.size}`)
const type = computed(() => (typeof props.field.type !== 'undefined' ? props.field.type : 'text'))

function input(e: Event): void {
  const target = e.target as HTMLInputElement
  emit('update:value', target.type === "checkbox" ? target.checked:target.value )
  console.log('checked--->',target.checked);
  console.log( 'type--->',target.type);

}
</script>

<template>
  <template v-if="type === 'switch'">
    <div class="form-check form-switch">
      <input  class="form-check-input" type="checkbox" :checked="value" @input="input" />
    </div>
  </template>
  <template  v-else>
    <input :class="sizeClass" :name="field.name" :type="type" :value="value" class="form-control" @input="input"/>
  </template>
</template>