<script setup>
    import {ref, watch} from 'vue'

    const emit = defineEmits(['update:modelValue'])
    const props = defineProps({
        disabled: {type: Boolean},
        field: {required: true, type: Object},
        form: {required: true, type: String},
        id: {required: true, type: String},
        modelValue: {type: Boolean}
    })
    const ratingValue = ref(null)

    console.log('ratingValue', ratingValue)

    function input(e) {
        console.log('eeeeee', e.target.value)
        ratingValue.value = e.target.value
    }
</script>

<template>
    <div class="d-flex">
        <div class="container">
            <div class="rating">
                <input
                    id="star5"
                    v-model="ratingValue"
                    type="radio"
                    name="rating"
                    value="5"/>
                <label for="star5" class="star" @click="input"><Fa icon="star"/> </label>
                <input
                    id="star4"
                    v-model="ratingValue"
                    type="radio"
                    name="rating"
                    value="4"/>
                <label for="star4" class="star" @click="input"><Fa icon="star"/> </label>
                <input
                    id="star3"
                    v-model="ratingValue"
                    type="radio"
                    name="rating"
                    value="3"/>
                <label for="star3" class="star" @click="input"><Fa icon="star"/> </label>
                <input
                    id="star2"
                    v-model="ratingValue"
                    type="radio"
                    name="rating"
                    value="2"/>
                <label for="star2" class="star" @click="input"><Fa icon="star"/> </label>
                <input
                    id="star1"
                    v-model="ratingValue"
                    type="radio"
                    name="rating"
                    value="1"/>
                <label for="star1" class="star" @click="input"><Fa icon="star"/> </label>
            </div>
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

.rating label:before {
  content: "\2605";
  position: absolute;
  opacity: 0;
}

.rating label.checked:before {
  color: #ffc107;
}

.rating input:checked ~ label:before {
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
/* input#rate-5:checked ~ label {
  color: rgb(255, 119, 178);
  text-shadow: 0 0 20px #952;
} */
/* .rate {
  border-bottom-right-radius: 6px;
  border-bottom-left-radius: 6px;
  border-top-right-radius: 6px;
  border-top-left-radius: 6px;
}

.rating {
  display: flex;
  flex-direction: row-reverse;
  justify-content: center
}

.rating>input {
  display: none
}

.rating label {
  position: relative;
  width: 1em;
  font-size: 20px;
  font-weight: 300;
  color: #FFD600;
  cursor: pointer;
  transition: all 0.2s ease;

}

/* .rating>label::before {
  content: "\2605";
  position: absolute;
  opacity: 0
} */

/* input#rate-:checked ~ label{
  color: #fd4;
}
.rating>label:hover:before,
.rating>label:hover~label:before {
  opacity: 1 !important
}

.rating>input:checked~label:before {
  opacity: 1
}

.rating:hover>input:checked~label:before {
  opacity: 0.4
}  */
</style>
