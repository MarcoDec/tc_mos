<script setup>
    import {computed, defineProps, onBeforeUnmount, onMounted, ref} from 'vue'
    import anime from 'animejs'
    import moment from 'moment-timezone'
    let interval = null
    const DEGREES_TO_MOVE_SECOND_HAND = 360 / 60
    const DEGREES_TO_MOVE_MINUTE_HAND = 360 / 60
    const DEGREES_TO_MOVE_HOUR_HAND = 360 / 12
    const SECONDS_IN_HOUR = 60 * 60
    const SECONDS_IN_MINUTE = 60

    const props = defineProps({
        country: {default: null, type: String},
        hourHandColor: {default: '#000', type: String},
        hourHandHeight: {default: 15, type: Number},
        hourHandTail: {default: 0, type: Number},
        hourHandWidth: {default: 1, type: Number},
        hourMarkerColor: {default: '#000', type: String},
        hourMarkerHeight: {default: 100, type: Number},
        hourMarkerWidth: {default: 1, type: Number},
        hourMarkers: {type: Boolean},
        innerBackground: {default: '#fff', type: String},
        innerBorder: {default: 'none', type: String},
        innerDiameter: {default: 90, type: Number},
        minuteHandColor: {default: '#000', type: String},
        minuteHandHeight: {default: 40, type: Number},
        minuteHandTail: {default: 0, type: Number},
        minuteHandWidth: {default: 1, type: Number},
        outerBackground: {default: 'none', type: String},
        outerBorder: {default: '1px solid #000', type: String},
        outerDiameter: {default: 100, type: Number},
        secondHand: {type: Boolean},
        secondHandColor: {default: '#000', type: String},
        secondHandHeight: {default: 50, type: Number},
        secondHandTail: {default: 0, type: Number},
        secondHandWidth: {default: 1, type: Number}
    })

    const paye = computed(() => props.country.replace('/', '-'))

    const hrMarkers = computed(() => [...Array(12).keys()].map(i => i + 1))
    const minMarkers = computed(() => [...Array(60).keys()].map(i => i + 1))
    const hours = ref(0)
    const minutes = ref(0)
    const seconds = ref(0)

    const hourMarkerHeightPX = computed(() => `${props.hourMarkerHeight.toString()}px`)
    const hourMarkerWidthPX = computed(() => `${props.hourMarkerWidth.toString()}px`)
    const hourHandHeightPX = computed(() => `${props.hourHandHeight.toString()}px`)
    const hourHandWidthPX = computed(() => `${props.hourHandWidth.toString()}px`)
    // const hourHandTailPX = computed(() => `${props.hourHandTail.toString()}px`)
    const minuteHandHeightPX = computed(() => `${props.minuteHandHeight.toString()}px`)
    const minuteHandWidthPX = computed(() => `${props.minuteHandWidth.toString()}px`)
    // const minuteHandTailPX = computed(() => `${props.minuteHandTail.toString()}px`)
    const secondHandHeightPX = computed(() => `${props.secondHandHeight.toString()}px`)
    const secondHandWidthPX = computed(() => `${props.secondHandWidth.toString()}px`)
    // const secondHandTailPX = computed(() => `${props.secondHandTail.toString()}px`)
    const innerDiameterPX = computed(() => `${props.innerDiameter.toString()}px`)
    const outerDiameterPX = computed(() => `${props.outerDiameter.toString()}px`)

    const clockOuterRadius = computed(() => props.outerDiameter / 2)
    const clockOuterRadiusPX = computed(() => `${clockOuterRadius.value.toString()}px`)
    // const clockInnerRadius = computed(() => props.innerDiameter / 2)
    // const clockInnerRadiusPX = computed(() => `${clockInnerRadius.value.toString()}px`)
    const clockInnerOriginXY = computed(() => (props.outerDiameter - props.innerDiameter) / 2)
    const clockInnerOriginXYPX = computed(() => `${clockInnerOriginXY.value.toString()}px`)
    const hourMarkerWidthQuarter = computed(() => props.hourMarkerWidth * 2)
    const hourMarkerWidthQuarterPX = computed(() => `${hourMarkerWidthQuarter.value.toString()}px`)
    const hourMarkerHeightQuarter = computed(() => props.hourMarkerHeight * 2)
    const hourMarkerHeightQuarterPX = computed(() => `${hourMarkerHeightQuarter.value.toString()}px`)
    const hourHandRotateOriginY = computed(() => props.hourHandHeight - props.hourHandTail)
    const hourHandRotateOriginYPX = computed(() => `${hourHandRotateOriginY.value.toString()}px`)
    const hourHandOriginY = computed(() => clockOuterRadius.value * -1 - props.hourHandTail)
    const hourHandOriginYPX = computed(() => `${hourHandOriginY.value.toString()}px`)
    const minuteHandRotateOriginY = computed(() => props.minuteHandHeight - props.minuteHandTail)
    const minuteHandRotateOriginYPX = computed(() => `${minuteHandRotateOriginY.value.toString()}px`)
    const minuteHandOriginY = computed(() => clockOuterRadius.value * -1 - props.minuteHandTail)
    const minuteHandOriginYPX = computed(() => `${minuteHandOriginY.value.toString()}px`)
    const secondHandRotateOriginY = computed(() => props.secondHandHeight - props.secondHandTail)
    const secondHandRotateOriginYPX = computed(() => `${secondHandRotateOriginY.value.toString()}px`)
    const secondHandOriginY = computed(() => clockOuterRadius.value * -1 - props.secondHandTail)
    const secondHandOriginYPX = computed(() => `${secondHandOriginY.value.toString()}px`)

    const leftHand = computed(() => clockOuterRadius.value - props.hourHandWidth / 2)
    const leftHandPX = computed(() => `${leftHand.value.toString()}px`)
    const leftMarker = computed(() => clockOuterRadius.value - props.hourMarkerWidth / 2)
    const leftMarkerPX = computed(() => `${leftMarker.value.toString()}px`)
    const leftHourMarker = computed(() => clockOuterRadius.value - hourMarkerWidthQuarter.value / 2)
    const leftHourMarkerPX = computed(() => `${leftHourMarker.value.toString()}px`)
    const leftMinuteHand = computed(() => clockOuterRadius.value - props.minuteHandWidth / 2)
    const leftMinuteHandPX = computed(() => `${leftMinuteHand.value.toString()}px`)
    const leftMinuteMarker = computed(() => clockOuterRadius.value - 3 / 2)
    const leftMinuteMarkerPX = computed(() => `${leftMinuteMarker.value.toString()}px`)
    const leftSecondHand = computed(() => clockOuterRadius.value - props.minuteHandWidth / 2)
    const leftSecondHandPX = computed(() => `${leftSecondHand.value.toString()}px`)

    function setHourHand() {
        const totalSeconds = minutes.value * SECONDS_IN_MINUTE + seconds.value
        const offset = totalSeconds * (DEGREES_TO_MOVE_HOUR_HAND / SECONDS_IN_HOUR)
        anime.set(`.hour-hand.${paye.value}`, {
            rotate: hours.value * DEGREES_TO_MOVE_HOUR_HAND + offset
        })
    }
    function setMinuteHand() {
        const offset = seconds.value * (DEGREES_TO_MOVE_MINUTE_HAND / SECONDS_IN_MINUTE)
        anime.set('.minute-hand', {
            rotate: minutes.value * DEGREES_TO_MOVE_MINUTE_HAND + offset
        })
    }
    function setSecondHand() {
        anime.set('.second-hand', {
            rotate: seconds.value * DEGREES_TO_MOVE_SECOND_HAND
        })
    }
    function animateHourHand() {
        setHourHand()
    }
    function animateMinuteHand() {
        setMinuteHand()
    }
    function animateSecondHand() {
        // immediately set rotation less than actual value
        anime.set('.second-hand', {
            rotate: (seconds.value - 1) * DEGREES_TO_MOVE_SECOND_HAND
        })
        // then animate to actual value
        anime({
            rotate: seconds.value * DEGREES_TO_MOVE_SECOND_HAND,
            targets: '.second-hand'
        })
    }
    function activate() {
        setHourHand()
        setMinuteHand()
        setSecondHand()
        interval = setInterval(() => {
            const time = moment().tz(props.country)
            hours.value = time.hours()
            minutes.value = time.minutes()
            seconds.value = time.seconds()
            animateHourHand()
            animateMinuteHand()
            animateSecondHand()
        }, 1000)
    }

    function hourMarkerStyle(hour) {
        return {transform: `rotate(${hour * DEGREES_TO_MOVE_HOUR_HAND}deg)`}
    }
    function minuteMarkerStyle(minute) {
        return {
            transform: `rotate(${minute * DEGREES_TO_MOVE_MINUTE_HAND}deg)`
        }
    }

    onMounted(() => {
        activate()
    })
    onBeforeUnmount(() => {
        clearInterval(interval)
        interval = null
    })
</script>

<template>
    <div class="clock">
        <div v-if="hourMarkers">
            <div
                v-for="hour in hrMarkers"
                v-once
                :key="hour"
                :style="hourMarkerStyle(hour)"
                class="hour-marker"/>
        </div>
        <div>
            <div
                v-for="minute in minMarkers"
                v-once
                :key="minute"
                :style="minuteMarkerStyle(minute)"
                class="minute-marker"/>
        </div>
        <div class="clock-outer"/>
        <div class="clock-inner"/>
        <div class="hand hour-hand" :class="paye"/>
        <div class="hand minute-hand"/>
        <div v-if="secondHand" class="hand second-hand"/>
    </div>
</template>

<style scoped>
.clock {
  position: relative;
}

.clock-outer {
  background: v-bind("outerBackground");
  border: v-bind("outerBorder");
  border-radius:v-bind("clockOuterRadiusPX");
  position: absolute;
  width: v-bind("outerDiameterPX");
  height: v-bind("outerDiameterPX");
}

.clock-inner {
  background: v-bind("innerBackground");
  border: v-bind("innerBorder");
  border-radius: v-bind("clockOuterRadiusPX");
  position: absolute;
  top: v-bind("clockInnerOriginXYPX");
  left: v-bind("clockInnerOriginXYPX");
  width: v-bind("innerDiameterPX");
  height: v-bind("innerDiameterPX");
}

.hand {
  position: absolute;
  transform-origin: bottom center;
}

.hour-hand {
  background-color: v-bind("hourHandColor");
  left: v-bind("leftHandPX");
  bottom: v-bind("hourHandOriginYPX");
  width: v-bind("hourHandWidthPX");
  height: v-bind("hourHandHeightPX");
  transform-origin: center v-bind("hourHandRotateOriginYPX");
}

.hour-marker {
  background-color: v-bind("hourMarkerColor");
  position: absolute;
  top: 0;
  left: v-bind("leftMarkerPX");
  width: v-bind("hourMarkerWidthPX");
  height: v-bind("hourMarkerHeightPX");
  transform-origin: center v-bind("clockOuterRadiusPX");
}

.hour-marker:nth-child(3n) {
  left:v-bind("leftHourMarkerPX");
  width: v-bind("hourMarkerWidthQuarterPX");
  height:v-bind("hourMarkerHeightQuarterPX");
}

.minute-hand {
  background-color: v-bind("minuteHandColor");
  left: v-bind("leftMinuteHandPX");
  bottom: v-bind("minuteHandOriginYPX");
  width: v-bind("minuteHandWidthPX");
  height: v-bind("minuteHandHeightPX");
  transform-origin: center v-bind("minuteHandRotateOriginYPX");
}

/**
 * TODO: Make minute markers customizable
 */

.minute-marker {
  background-color: #666;
  border-radius: 3px;
  position: absolute;
  top: 0;
  left: v-bind("leftMinuteMarkerPX");
  width: 3px;
  height: 3px;
  transform-origin: center v-bind("clockOuterRadiusPX");
}

.minute-marker:nth-child(5n) {
  display: none;
}

.second-hand {
  background-color: v-bind("secondHandColor");
  left: v-bind("leftSecondHandPX");
  bottom: v-bind("secondHandOriginYPX");
  width: v-bind("secondHandWidthPX");
  height: v-bind("secondHandHeightPX");
  transform-origin: center v-bind("secondHandRotateOriginYPX");
}
</style>
