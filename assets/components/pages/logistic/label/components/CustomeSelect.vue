<script setup>
    import {defineProps, ref, defineEmits} from 'vue'

    const emits = defineEmits(['update:modelValue'])
    defineProps({
        options: {required: true, type: Array},
        title: {default: '', type: String}
    })
    const showDropdown = ref(false)
    const selectedOption = ref(null)
    function toggleDropdown() {
        showDropdown.value = !showDropdown.value
    }
    const selectOption = option => {
        selectedOption.value = option
        showDropdown.value = false
        emits('update:modelValue', option)
    }
</script>

<template>
    <div class="custom-select">
        <div v-if="title !== ''" class="title">
            {{ title }}
        </div>
        <Fa :brand="false" icon="chevron-down" class="float-end"/>
        <div class="selected-value" @click="toggleDropdown">
            <Fa v-if="selectedOption" :style="{color: selectedOption.color}" :brand="false" icon="fa-square"/>
            {{ selectedOption ? selectedOption.name : '' }}
        </div>
        <div v-if="showDropdown" class="options-container">
            <div
                v-for="option in options"
                :key="option.value"
                class="align-items-center d-flex flex-row option"
                @click="selectOption(option)">
                <Fa :style="{color: option.color}" :brand="false" icon="fa-square"/>
                {{ option.name }}
            </div>
        </div>
    </div>
</template>

<style scoped>
    .custom-select {
        position: relative;
        border: 1px solid #a0a0a0;
        //border: 1px solid black;
        cursor: pointer;
        user-select: none;
    }

    .title {
        font-size: 10px;
        font-weight: bold;
        position: relative;
        top: 0px;
        left: 0px;
        padding: 5px;
    }
    .selected-value {
        padding: 10px;
        background-color: #fff;
    }
    .selected-value:hover {
        background-color: #70ccf4;
    }
    .options-container {
        position: absolute;
        width: 100%;
        background-color: #fff;
        border: 1px solid #ccc;
        z-index: 1;
    }

    .option {
        padding: 10px;
        transition: background-color 0.2s;
    }

    .option:hover {
        background-color: #70ccf4;
    }
</style>
