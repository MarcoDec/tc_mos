<script setup>
    import {ref, computed, nextTick, onMounted, onBeforeUnmount, useAttrs} from 'vue'
    import {FontAwesomeIcon} from "@fortawesome/vue-fontawesome"

    // Références pour les éléments DOM
    const gui = ref(null)
    const guiHeader = ref(null)
    const leftElement = ref(null)
    const rightElement = ref(null)
    const bottomElement = ref(null)
    const attrs = useAttrs()

    // Etat pour le plein écran
    const isFullscreen = ref({
        left: false,
        right: false,
        bottom: false
    })

    // Proportions de l'interface utilisateur
    const guiRatio = ref(0.5)

    // Dimensions de la fenêtre
    const windowSize = ref({height: window.innerHeight, width: window.innerWidth})
    const appNavBarHeight = computed(() => document.getElementById('app-nav-bar').getBoundingClientRect().height)

    // Styles calculés
    const guiWrapperStyle = computed(() => ({
        height: `${windowSize.value.height - appNavBarHeight.value - 5}px`
    }))

    const guiHeaderStyle = computed(() => ({
        height: 'auto',
        top: `${appNavBarHeight.value}px`,
        width: '100%',
        zIndex: '1'
    }))

    const guiStyle = ref({})

    const guiTopStyle = computed(() => ({
        minHeight: `${guiRatio.value * windowSize.value.height}px`,
        maxHeight: `${guiRatio.value * windowSize.value.height}px`,
        height: `${guiRatio.value * windowSize.value.height}px`
    }))

    const guiBottomStyle = computed(() => ({
        minHeight: `${(1 - guiRatio.value) * windowSize.value.height}px`,
        maxHeight: `${(1 - guiRatio.value) * windowSize.value.height}px`,
        height: `${(1 - guiRatio.value) * windowSize.value.height}px`
    }))

    // Mettre à jour le style de l'interface utilisateur
    function updateGuiStyle() {
        if (guiHeader.value) {
            guiStyle.value = {
                height: `${windowSize.value.height - appNavBarHeight.value - guiHeader.value.getBoundingClientRect().height}px`,
                paddingTop: `${guiHeader.value.getBoundingClientRect().height}px`
            }
        }
    }

    // Fonction de redimensionnement de la fenêtre
    function onWindowResize() {
        windowSize.value = {
            height: window.innerHeight,
            width: window.innerWidth
        }
        updateGuiStyle()
    }
    // Fonction de mise à jour du plein écran
    function toggleFullscreen(section) {
        isFullscreen.value[section] = !isFullscreen.value[section]

        if (section === 'left' || section === 'right') {
            isFullscreen.value.bottom = false
            const element = section === 'left' ? leftElement.value : rightElement.value
            const otherElement = section === 'left' ? rightElement.value : leftElement.value

            if (isFullscreen.value[section]) {
                guiRatio.value = 1
                element.style.maxWidth = '100%'
                element.style.minWidth = '100%'
                element.style.width = '100%'
                element.style.minHeight = '100%'
                bottomElement.value.style.display = 'none'
                otherElement.style.display = 'none'
            } else {
                guiRatio.value = 0.5
                element.style.maxWidth = '50%'
                element.style.minWidth = '50%'
                element.style.width = '50%'
                bottomElement.value.style.display = 'block'
                otherElement.style.display = 'block'
            }
        } else if (section === 'bottom') {
            isFullscreen.value.left = false
            isFullscreen.value.right = false
            guiRatio.value = isFullscreen.value.bottom ? 0 : 0.5
        }
        updateGuiStyle()
    }

    function resize(event) {
        const startY = event.clientY
        const startHeight = guiRatio.value * windowSize.value.height

        function onMouseMove(e) {
            const newHeight = startHeight + (e.clientY - startY)
            guiRatio.value = newHeight / windowSize.value.height
        }

        function onMouseUp() {
            window.removeEventListener('mousemove', onMouseMove)
            window.removeEventListener('mouseup', onMouseUp)
        }

        window.addEventListener('mousemove', onMouseMove)
        window.addEventListener('mouseup', onMouseUp)
    }

    onMounted(async () => {
        await nextTick()
        onWindowResize()
        window.addEventListener('resize', onWindowResize)
        updateGuiStyle()
    })

    onBeforeUnmount(() => {
        window.removeEventListener('resize', onWindowResize)
    })
</script>

<template>
    <div id="gui-wrapper" :style="guiWrapperStyle" v-bind="attrs">
        <div id="gui-header" ref="guiHeader" :style="guiHeaderStyle">
            <slot name="gui-header"/>
        </div>
        <div id="gui" ref="gui" :style="guiStyle">
            <div id="gui-top" :style="guiTopStyle">
                <div id="gui-left" ref="leftElement" class="bg-info parent-buttons-div" :class="{'full-screen': isFullscreen.left}">
                    <div class="bg-info gui-card" :class="{'full-visible-width': isFullscreen.left, 'half-visible-width': !isFullscreen.left}">
                        <slot name="gui-left"/>
                    </div>
                    <div class="full-screen-btn">
                        <FontAwesomeIcon
                            v-if="isFullscreen.left"
                            class="fullscreen-btn full-screen-button"
                            icon="fa-solid fa-circle-chevron-down"
                            title="Réduire la fenêtre en plein écran"
                            @click="toggleFullscreen('left')"/>
                        <FontAwesomeIcon
                            v-else
                            class="screen-btn full-screen-button"
                            icon="fa-solid fa-circle-chevron-up"
                            title="Agrandir la fenêtre en plein écran"
                            @click="toggleFullscreen('left')"/>
                    </div>
                </div>
                <div id="gui-right" ref="rightElement" class="bg-warning parent-buttons-div" :class="{'full-screen': isFullscreen.right}">
                    <div class="bg-warning gui-card" :class="{'full-visible-width': isFullscreen.right, 'half-visible-width': !isFullscreen.right}">
                        <slot name="gui-right"/>
                    </div>
                    <div class="full-screen-btn">
                        <FontAwesomeIcon
                            v-if="isFullscreen.right"
                            class="fullscreen-btn full-screen-button"
                            icon="fa-solid fa-circle-chevron-down"
                            title="Réduire la fenêtre en plein écran"
                            @click="toggleFullscreen('right')"/>
                        <FontAwesomeIcon
                            v-else
                            class="screen-btn full-screen-button"
                            icon="fa-solid fa-circle-chevron-up"
                            title="Agrandir la fenêtre en plein écran"
                            @click="toggleFullscreen('right')"/>
                    </div>
                </div>
            </div>
            <hr class="gui-resizer" @mousedown="resize"/>
            <div id="gui-bottom" ref="bottomElement" class="bg-danger parent-buttons-div" :style="guiBottomStyle" :class="{'full-screen': isFullscreen.bottom}">
                <div class="bg-danger gui-card">
                    <slot name="gui-bottom"/>
                </div>
                <div class="full-screen-btn">
                    <FontAwesomeIcon
                        v-if="isFullscreen.bottom"
                        class="fullscreen-btn full-screen-button"
                        icon="fa-solid fa-circle-chevron-down"
                        title="Réduire la fenêtre en plein écran"
                        @click="toggleFullscreen('bottom')"/>
                    <FontAwesomeIcon
                        v-else
                        class="screen-btn full-screen-button"
                        icon="fa-solid fa-circle-chevron-up"
                        title="Agrandir la fenêtre en plein écran"
                        @click="toggleFullscreen('bottom')"/>
                </div>
            </div>
        </div>
    </div>
</template>

<style scoped>
#gui-header {
  position: fixed;
}

#gui {
  min-width: 100%;
  display: flex;
  flex-direction: column;
  align-items: stretch;
}

#gui-top {
  min-width: 100%;
  max-width: 100%;
  display: flex;
  flex-direction: row;
  align-items: stretch;
}

#gui-left, #gui-right {
  min-width: 50%;
  max-width: 50%;
  overflow: auto;
  position: relative;
}

.gui-resizer {
  background-color: green;
  cursor: row-resize;
  margin: 2px;
  padding: 5px;
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

.fullscreen-btn, .screen-btn {
  position: absolute;
  top: 0;
  right: 0;
  transform: translateX(0);
  z-index: 2;
  background: transparent;
  border: none;
  cursor: pointer;
}
</style>
