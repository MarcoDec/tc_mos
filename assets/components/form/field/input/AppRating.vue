<script setup>
    import {defineProps} from 'vue'
    defineProps({disabled: Boolean})
</script>

<template>
    <div class="d-flex">
        <div class="bg-success rate">
            <div class="rating">
                <input id="5" type="radio" name="rating" value="5" :disabled="disabled"/><label for="5">☆</label>
                <input id="4" type="radio" name="rating" value="4" :disabled="disabled"/><label for="4">☆</label>
                <input id="3" type="radio" name="rating" value="3" :disabled="disabled"/><label for="3">☆</label>
                <input id="2" type="radio" name="rating" value="2" :disabled="disabled"/><label for="2">☆</label>
                <input id="1" type="radio" name="rating" value="1" :disabled="disabled"/><label for="1">☆</label>
            </div>
        </div>
    </div>
</template>

<style scoped>
.rate {
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

.rating>label {
  position: relative;
  width: 1em;
  font-size: 17px;
  font-weight: 300;
  color: #FFD600;
  cursor: pointer
}

.rating>label::before {
  content: "\2605";
  position: absolute;
  opacity: 0
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
}
</style>
