<script setup>
    defineProps({
        violations: {default: () => [], type: Array}
    })
    const emit = defineEmits(['close'])
    function close(){
        emit('close')
    }
</script>

<template>
    <div>
        <div class="popup-wrapper">
            <div class="popup">
                <div v-for="violation in violations" :key="violation">
                    {{ violation.message }}
                </div>
                <button @click="close">
                    Close
                </button>
            </div>
        </div>
    </div>
</template>

<style scoped>
.popup-wrapper {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  background-color: rgba(0, 0, 0, 0.5);
  display: flex;
  justify-content: center;
  align-items: center;
}

.popup {
  background-color: white;
  padding: 20px;
  border-radius: 5px;
  box-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
  text-align: center;
}
</style>
