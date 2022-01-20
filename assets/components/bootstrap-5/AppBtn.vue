<script lang="ts" setup>
  import type {BootstrapSize, BootstrapVariant} from '../../types/bootstrap-5'
  import {computed, defineProps, withDefaults} from 'vue'
  const props = withDefaults(
      defineProps<{
        icon?: string | null
        size?: BootstrapSize
        type?: 'button' | 'reset' | 'submit'
        variant?: BootstrapVariant
      }>(),
      {
        icon: null,
        size: 'sm',
        type: 'button',
        variant: 'primary'
      }
  )
  const btnClass = computed(() => ({
    [`btn-${props.size} btn-${props.variant}`]: true,
    'btn-icon': Boolean(props.icon)
  }))
</script>

<template>
  <button :class="btnClass" :type="type" class="btn">
    <slot>
      <Fa v-if="icon" :icon="icon"/>
    </slot>
  </button>
</template>
