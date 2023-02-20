<template>
    <div class="container">
        <ClockAnalog
            outer-background="none"
            outer-border="none"
            :outer-diameter="300"
            inner-background="none"
            inner-border="none"
            :inner-diameter="290"
            :hour-markers="true"
            hour-marker-color="#000"
            :hour-marker-width="1"
            :hour-marker-height="10"
            hour-hand-color="#111"
            :hour-hand-width="4"
            :hour-hand-height="100"
            :hour-hand-tail="6"
            minute-hand-color="#222"
            :minute-hand-width="2"
            :minute-hand-height="130"
            :minute-hand-tail="6"
            :second-hand="true"
            second-hand-color="#600"
            :second-hand-width="1"
            :second-hand-height="140"
            :second-hand-tail="10"
            country="Africa/Tunis"/>
    </div>
    <br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/><br/>
    <div>
        <ClockAnalog
            outer-background="none"
            outer-border="none"
            :outer-diameter="60"
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
            :minute-hand-width="1"
            :minute-hand-height="23"
            :minute-hand-tail="6"
            :second-hand="true"
            second-hand-color="#600"
            :second-hand-width="1"
            :second-hand-height="28"
            :second-hand-tail="7"
            country="Europe/paris"/>
    </div>
</template>

<style>
* {
  box-sizing: border-box;
  margin: 0;
  padding: 0;
}

.container {
  padding: 20px;
}
</style>
