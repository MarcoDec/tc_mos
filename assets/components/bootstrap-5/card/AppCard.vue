<script setup>
    defineProps({
        title: {required: true, type: String},
        titleTag: {default: 'h2', type: String}
    })
</script>

<template>
    <div class="card">
        <component :is="titleTag" class="card-title">
            {{ title }}
        </component>
        <div class="card-body">
            <slot/>
        </div>
    </div>
</template>
