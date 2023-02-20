<script setup>
    import {computed, defineProps, ref} from 'vue'
    import ClockAnalog from '../../clock/ClockAnalog.vue'
    import moment from 'moment-timezone'
    const props = defineProps({
        country: {required: true, type: String},
        timezone: {required: true, type: String}
    })
    const currentTime = ref(moment().tz(props.timezone))
    function updateCurrentTime() {
        currentTime.value = moment().tz(props.timezone)
    }
    setInterval(() => updateCurrentTime(), 1 * 1000)
    const timeDigitale = computed(() => currentTime.value.format('HH:mm:ss'))
</script>

<template>
    <div class="row">
        <div class="col-5 ms-1">
            <ClockAnalog
                outer-background="none"
                outer-border="none"
                :outer-diameter="50"
                inner-background="none"
                inner-border="none"
                :inner-diameter="29"
                :hour-markers="true"
                hour-marker-color="#000"
                :hour-marker-width="1"
                :hour-marker-height="4"
                hour-hand-color="#111"
                :hour-hand-width="2"
                :hour-hand-height="18"
                :hour-hand-tail="6"
                minute-hand-color="#222"
                :minute-hand-width="2"
                :minute-hand-height="23"
                :minute-hand-tail="6"
                :second-hand="true"
                second-hand-color="#600"
                :second-hand-width="1"
                :second-hand-height="28"
                :second-hand-tail="7"
                :country="props.timezone"/>
        </div>
        <div class="col">
            <AppBtn label="pays" variant="dark">
                {{ country }}
            </AppBtn>
            {{ timeDigitale }}
        </div>
    </div>
</template>

