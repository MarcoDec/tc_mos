<script setup>
    import {defineProps, defineEmits, computed} from 'vue'
    const emits = defineEmits(['click', 'edit', 'remove'])
    const props = defineProps({
        icon: {required: true, type: String},
        iconColor: {default: 'black', type: String},
        label: {required: true, type: String},
        labelColor: {default: 'black', type: String},
        textColor: {default: 'black', type: String},
        text: {required: true, type: String},
        offset: {default: 0, type: Number},
        showEdit: {default: true, type: Boolean},
        showRemove: {default: true, type: Boolean}
    })
    function clickEdit() {
        emits('edit')
    }
    function clickRemove() {
        emits('remove')
    }
    function click() {
        emits('click')
    }
    const leftOffset = computed(() => {
        return (props.offset + 50) + 'px'
    })
</script>

<template>
    <div class="icon-with-text" @click="click">
        <Fa class="icon" :brand="false" :icon="icon" :style="{color: iconColor, left: leftOffset}"/>
        <div class="label" :style="{color: labelColor, left: leftOffset}">{{ label }}</div>
        <div class="text" :style="{color: textColor, left: leftOffset}">{{ text }}</div>
        <div class="buttons" :style="{left: leftOffset}">
            <Fa v-if="showEdit === true" :brand="false" icon="pencil" class="myButton text-primary" title="edit" @click="clickEdit"/>
            <Fa v-if="showRemove === true" :brand="false" icon="trash" class="myButton text-danger" title="remove" @click="clickRemove"/>
        </div>
    </div>
</template>

<style scoped>
    .icon-with-text {
        position: relative;
        display: inline-block; /* ou 'block' selon vos besoins */
        min-width: 120px;
        max-width: 120px;
    }

    .icon {
        font-size: 100px; /* Ajustez la taille de l'icône */
        background-color: lightgrey;
        padding: 10px;
        border-radius: 20px;
    }
    .icon:hover {
        box-shadow: #212529 0px 0px 10px 0px;
    }
    .text {
        position: absolute;
        top: 70%; /* Centrer verticalement */
        //left: 50px; /* Centrer horizontalement */
        transform: translate(-50%, -50%); /* Ajustement fin pour le centrage */
        font-size: 12px;
        text-align: center;
        /* Autres styles pour le texte */
    }
    .label {
        position: absolute;
        top: 20%; /* Centrer verticalement */
        //left: 55px; /* Centrer horizontalement */
        transform: translate(-50%, -50%); /* Ajustement fin pour le centrage */
        font-weight: bold;
        font-size: 15px;
        //border: 1px solid red;
        min-width: 120px;
        max-width: 120px;
        text-align: center;
        /* Autres styles pour le texte */
    }
    .buttons {
        position: absolute;
        padding: 3px;
        display: flex;
        flex-direction: row;
        justify-content: space-around;
        min-width: 100px;
        top: 90%; /* Centrer verticalement */
        //left: 50px; /* Centrer horizontalement */
        transform: translate(-50%, -50%); /* Ajustement fin pour le centrage */
        /* Autres styles pour le texte */
    }
    .myButton:hover {
        //box-shadow: #212529 0px 0px 10px 0px;
        font-size: 25px;
        box-shadow: inset white 0px 0px 10px 0px;
    }
</style>
