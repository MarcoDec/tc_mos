<script setup>
    import {computed, nextTick, onBeforeUnmount, onMounted, onUpdated, ref} from 'vue'

    const gui = ref(null)
    const guiHeader = ref(null)
    // Ratio de la zone top par rapport à la zone top+bottom
    const guiRatio = ref(0.5)
    // const gui = ref()
    // Dimensions de la fenêtre du navigateur
    const windowSize = ref({height: window.innerHeight, width: window.innerWidth})
    // Hauteur de la barre de menu
    const appNavBarHeight = computed(() => document.getElementById('app-nav-bar').getBoundingClientRect().height)
    // Styles à appliquer au wrapper
    const guiWrapperStyle = ref({height: '50vh'})
    // Styles à appliquer à la ligne d'entête
    const guiHeaderStyle = ref({})
    // Styles à appliquer à la zone sous-entête
    const guiStyle = ref({})
    // Styles à appliquer à la zone top
    const guiTopStyle = ref({
    })
    // Styles à appliquer à la zone bottom
    const guiBottomStyle = ref({
    })
    const zoneUtile = ref({
        x0: 0, //en px
        x1: 100, //en px
        xs: 0, // position x souris 'screen'
        y0: 100, //en px
        y1: 200, //en px
        ys: 0 // position y souris 'screen'
    })
    const guiTopStyleComputed = computed(() => {
        if (guiTopStyle.value) return {
            'min-height': guiTopStyle.value['min-height'],
            'max-height': guiTopStyle.value['max-height'],
            height: guiTopStyle.value.height
        }
        return {
            'min-height': 0,
            'max-height': 0,
            height: 0
        }
    })
    const guiBottomStyleComputed = computed(() => {
        if (guiBottomStyle.value) return {
            'min-height': guiBottomStyle.value['min-height'],
            'max-height': guiBottomStyle.value['max-height'],
            height: guiBottomStyle.value.height
        }
        return {
            'min-height': 0,
            'max-height': 0,
            height: 0
        }
    })
    const guiHeaderComputed = computed(() => {
        if (guiHeader.value) return {height: guiHeader.value.getBoundingClientRect().height}
        return {height: 0}
    })
    function handleMouseMove(event) {
        if (event.pageX === null && event.clientX !== null) {
            const eventDoc = event.target && event.target.ownerDocument || document
            const doc = eventDoc.documentElement
            const body = eventDoc.body

            event.pageX = event.clientX + (doc && doc.scrollLeft || body && body.scrollLeft || 0) - (doc && doc.clientLeft || body && body.clientLeft || 0)
            event.pageY = event.clientY + (doc && doc.scrollTop || body && body.scrollTop || 0) - (doc && doc.clientTop || body && body.clientTop || 0)
        }

        // Use event.pageX / event.pageY here
        zoneUtile.value.xs = event.pageX
        zoneUtile.value.ys = event.pageY
    }

    function onRatioUpdate() {
        guiTopStyle.value['min-height'] = `${guiRatio.value * (zoneUtile.value.y1 - zoneUtile.value.y0)}px`
        guiTopStyle.value['max-height'] = `${guiRatio.value * (zoneUtile.value.y1 - zoneUtile.value.y0)}px`
        guiTopStyle.value.height = `${guiRatio.value * (zoneUtile.value.y1 - zoneUtile.value.y0)}px`
        guiBottomStyle.value['min-height'] = `${(1 - guiRatio.value) * (zoneUtile.value.y1 - zoneUtile.value.y0)}px`
        guiBottomStyle.value['max-height'] = `${(1 - guiRatio.value) * (zoneUtile.value.y1 - zoneUtile.value.y0)}px`
        guiBottomStyle.value.height = `${(1 - guiRatio.value) * (zoneUtile.value.y1 - zoneUtile.value.y0)}px`
    }

    function onWindowResize() {
        // appNavBarHeight.value = document.getElementById('app-nav-bar').getBoundingClientRect().height
        windowSize.value = {
            height: window.innerHeight,
            width: window.innerWidth
        }
        guiWrapperStyle.value.height = `${windowSize.value.height - appNavBarHeight.value - 5}px`
        guiHeaderStyle.value = {
            height: 'auto',
            top: `${appNavBarHeight.value}px`,
            width: '100%',
            'z-index': '1'
        }
        zoneUtile.value.guiTop = document.getElementById('gui-header').getBoundingClientRect().height - 3
        zoneUtile.value.x1 = windowSize.value.width
        zoneUtile.value.y0 = appNavBarHeight.value + zoneUtile.value.guiTop
        zoneUtile.value.y1 = windowSize.value.height
        guiStyle.value = {
            height: `${zoneUtile.value.y1 - zoneUtile.value.yo}px`,
            'max-height': `${zoneUtile.value.y1 - zoneUtile.value.yo}px`,
            'min-height': `${zoneUtile.value.y1 - zoneUtile.value.yo}px`,
            'padding-top': `${zoneUtile.value.guiTop}px`
        }
        onRatioUpdate()
    }

    function resize() {
        function drag(position) {
            if (position.y > zoneUtile.value.y0) {
                const ratio = (position.y - zoneUtile.value.y0) / (zoneUtile.value.y1 - zoneUtile.value.y0)
                if (ratio >= 0.1 && ratio <= 0.9) {
                    guiRatio.value = ratio
                    onRatioUpdate()
                }
            }
        }
        function stopDrag() {
            gui.value.removeEventListener('mousemove', drag)
            gui.value.removeEventListener('mouseup', stopDrag)
        }
        gui.value.addEventListener('mousemove', drag)
        gui.value.addEventListener('mouseup', stopDrag)
    }
    onMounted(async () => {
        await nextTick()
        onWindowResize()
        window.addEventListener('resize', onWindowResize)
        document.addEventListener('mousemove', handleMouseMove)
    })
    onUpdated(() => {
        onWindowResize()
    })
    onBeforeUnmount(() => {
        window.removeEventListener('resize', onWindowResize)
        document.removeEventListener('mousemove', handleMouseMove)
    })
</script>

<template>
    <div id="gui-wrapper" class="" :style="guiWrapperStyle">
        <div id="gui-header" ref="guiHeader" :style="guiHeaderStyle">
            <slot name="gui-header" :size="guiHeaderComputed"/>
        </div>
        <div id="gui" ref="gui" :style="guiStyle">
            <div id="gui-top" class="gui-top" :style="guiTopStyle">
                <div id="gui-left" class="bg-info">
                    <div class="bg-info gui-card">
                        <slot name="gui-left" :size="guiTopStyleComputed"/>
                    </div>
                </div>
                <div id="gui-right" class="bg-warning">
                    <div class="bg-warning gui-card">
                        <slot name="gui-right" :size="guiTopStyleComputed">
                            <!--                            <div>guiTopStyle min-height: {{guiTopStyleComputed['min-height']}}</div>-->
                            <!--                            <div>guiTopStyle max-height: {{guiTopStyleComputed['max-height']}}</div>-->
                            <!--                            <div>guiTopStyle height: {{guiTopStyleComputed.height}}</div>-->
                            <!--                            <div>guiBottomStyle min-height: {{guiBottomStyleComputed['min-height']}}</div>-->
                            <!--                            <div>guiBottomStyle max-height: {{guiBottomStyleComputed['max-height']}}</div>-->
                            <!--                            <div>guiBottomStyle height: {{guiBottomStyleComputed.height}}</div>-->
                        </slot>
                    </div>
                </div>
            </div>
            <hr class="gui-resizer" @mousedown="resize"/>
            <div id="gui-bottom" class="bg-danger" :style="guiBottomStyle">
                <div class="bg-danger gui-card">
                    <slot name="gui-bottom" :size="guiBottomStyleComputed"/>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
    #gui-wrapper {
    }
    #gui-header {
       position: fixed;
    }
    #gui {
        min-width:100%;
        display:flex;
        flex-direction: column;
        align-items: stretch;
    }
    #gui-top {
        min-width:100%;
        max-width: 100%;
        display: flex;
        flex-direction: row;
        align-items: stretch;
    }
    #gui-left {
        min-width: 50%;
        max-width: 50%;
        overflow: auto;
    }
    #gui-right {
        min-width: 50%;
        max-width: 50%;
        overflow: auto;
    }
    .gui-resizer {
        background-color: black;
        cursor: row-resize;
        margin: 0;
        padding: 1px;
        display: block;
    }
    #gui-bottom {
        min-width: 100%;
        max-width: 100%;
        display: flex;
        flex-direction: row;
        align-items: stretch;
        overflow: auto;
    }
</style>
