<script lang="ts" setup>
    import {defineProps} from 'vue'
    defineProps<{disabled: boolean}>()
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
