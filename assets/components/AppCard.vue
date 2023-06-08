<script setup>
    defineProps({title: {required: true, type: String}})
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
