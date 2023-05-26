<script setup>
    const props = defineProps({title: {required: true, type: String}})
    console.log(props)
</script>

<template>
    <div class="bg-primary card">
        <h1 class="card-title">
            {{ title }}
        </h1>
        <div class="card-body">
            <slot/>
        </div>
    </div>
</template>
