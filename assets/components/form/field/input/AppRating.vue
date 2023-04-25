<script setup>
    import {defineEmits, defineProps, ref} from 'vue'

    const emit = defineEmits(['update:modelValue'])
    const props = defineProps({
        id: {required: true, type: String},
        form: {required: true, type: String},
        modelValue: {type: Boolean}
    })

    const ratingValue = ref(props.modelValue)
    console.log('fff', props.form)
    function input(value) {
        if (ratingValue.value === value) {
            ratingValue.value = 0
        } else {
            ratingValue.value = value
        }
        console.log('ratingValue.value', ratingValue.value)
        emit('update:modelValue', ratingValue.value)
    }
</script>

<template>
    <div :id="id" :form="form" class="container">
        <div class="rating">
            <label for="star5" class="star" @click="input(5)"><Fa :class="ratingValue >= 5 ? 'checked' : ''" icon="star"/>
            </label>
            <label for="star4" class="star" @click="input(4)"><Fa :class="ratingValue >= 4 ? 'checked' : ''" icon="star"/></label>
            <label for="star3" class="star" @click="input(3)"><Fa :class="ratingValue >= 3 ? 'checked' : ''" icon="star"/></label>
            <label for="star2" class="star" @click="input(2)"><Fa :class="ratingValue >= 2 ? 'checked' : ''" icon="star"/></label>
            <label for="star1" class="star" @click="input(1)"><Fa :class="ratingValue >= 1 ? 'checked' : ''" icon="star"/></label>
        </div>
    </div>
</template>

<style scoped>
.container {
  background: #22944e;
  border-radius: 6px;
}

.rating {
  display: flex;
  flex-direction: row-reverse;
  justify-content: center;
}

.rating label {
  font-size: 20px;
  font-weight: 300;
  color: #444;
  cursor: pointer;
  transition: all 0.2s ease;
}

.rating label.checked:before {
  color: #ffc107;
}

.rating label:hover ~ label:before {
  color: #ffdb70;
}

.rating label:hover:before {
  color: #ffc107;
}

input:not(:checked) ~ label:hover,
input:not(:checked) ~ label:hover ~ label {
  color: #fd4;
}

input:checked ~ label {
  color: #fd4;
}

.fa-star {
  color: #444;
}

.checked {
  color: #ffc107;
}
</style>
