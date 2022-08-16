<script setup>
    import {computed, defineProps} from 'vue'

    const props = defineProps({
        field: {required: true, type: Object},
        index: {required: true, type: Number},
        lengthFields: {required: true, type: Number}
    })
    const leftField = computed(() => `${props.index * 100}px`)
</script>

<template>
    <th v-if="props.index < lengthFields" class="first-col-field sticky-col-field">
        {{ field.label }}
    </th>
    <th v-else>
        {{ field.label }}
    </th>
</template>

<style scoped>
.sticky-col-field {
  position: -webkit-sticky;
  position: sticky !important ;
  background-color: #eee;
}
.first-col-field {
  width: 100px ;
  min-width: 100px;
  max-width: 100px;
  left: v-bind("leftField");
  z-index: 100
}
</style>
