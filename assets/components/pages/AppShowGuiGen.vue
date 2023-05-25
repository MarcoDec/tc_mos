<script setup>
    import {computed, ref} from 'vue'

    const guiRatio = ref(0.5)
    const guiRatioPercent = computed(() => `${guiRatio.value * 100}%`)

    function resize(e) {
        const gui = e.target.parentElement.parentElement
        const height = gui.offsetHeight
        const top = gui.offsetTop

        function drag(position) {
            const ratio = (position.y - top) / height
            if (ratio >= 0.1 && ratio <= 0.9)
                guiRatio.value = ratio
        }

        function stopDrag() {
            gui.removeEventListener('mousemove', drag)
            gui.removeEventListener('mouseup', stopDrag)
        }

        gui.addEventListener('mousemove', drag)
        gui.addEventListener('mouseup', stopDrag)
    }
</script>

<template>
    <div class="gui-wrapper">
        <div class="gui-header">
            <slot name="gui-header"/>
        </div>
        <div class="gui">
            <div class="gui-left">
                <div class="gui-card">
                    <slot name="gui-left"/>
                </div>
            </div>
            <div class="gui-right">
                <div class="gui-card">
                    <slot name="gui-right"/>
                </div>
            </div>
            <div class="gui-bottom">
                <div class="gui-card">
                    <slot name="gui-bottom"/>
                </div>
                <hr class="gui-resizer" @mousedown="resize"/>
            </div>
        </div>
    </div>
</template>

<style scoped>
    .gui {
        --gui-ratio: v-bind(guiRatioPercent);
    }
</style>
