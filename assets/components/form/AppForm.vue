<script setup>
    defineProps({id: {required: true, type: String}})

    const emit = defineEmits(['submit'])
    function submit(e) {
        e.preventDefault()
        emit('submit')
    }
</script>

<template>
    <form :id="id" autocomplete="off" enctype="multipart/form-data" method="post" novalidate @submit="submit">
        <slot/>
    </form>
</template>
