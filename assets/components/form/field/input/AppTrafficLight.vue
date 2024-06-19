<template>
    <div id="traffic-light">
        <input id="color1" type="radio" name="traffic-light-color"/>
        <input id="color2" type="radio" name="traffic-light-color"/>
        <input id="color3" type="radio" name="traffic-light-color"/>
    </div>
</template>

<style scoped>
body {
  background-image: url(https://f.cl.ly/items/2K0P3z2y3X3c052f2B3Y/Image%202013.04.30%2021%3A28%3A02.png);
}

#traffic-light {
  position: absolute;
  display: block;
  /* margin-left: 127px; */
  /* margin-top: -58px; */
  background-color: #333;
  width: 40px;
  height: 50px;
  border-radius: 5px;
  transform: rotate(90deg);
}
@media only screen and (max-width: 1px) {
#traffic-light {
    /* margin-left: 37px; */
    /* margin-top: -36px; */
    width: 38px;
    height: 50px;

}
input {
    appearance: none;
    /* position: relative; */
    /*left: 20%;*/
    width: 20px;
    height: 20px;
    /*margin-top: 10px;*/
    /*margin-right: -23px;*/
}
}
@media screen and (min-width: 1000px)   {
#traffic-light {
    /*margin-top: -60px;*/
    width: 33px;
    /*height: 150px;*/

}
input {
    appearance: none;
    /*position: relative;*/
    /*left: 20%;*/
    width: 20px;
    height: 20px;
    /*margin-top: 10px;*/
    /*margin-right: -23px;*/
}
}
input {
  appearance: none;
  /*position: relative;*/
  /*left: 20%;*/
  width: 20px;
  height: 20px;
  /*margin-top: 22px;*/
  /*margin-left: 0px;*/
  background-color: grey;
  vertical-align: middle;
  border-radius: 100%;
  display: block;
}
input #color1 {
  background-color: darken (#ff0000, 15%);
}

#color1:checked {
  background-color: #ff0000;
  box-shadow: 0 0 6em lighten(#ff0000, 10%);
}
input #color1:hover {
  animation: blink1 1.1s step-end infinite;
}

input #color2 {
  background-color: darken (#ffff00, 15%);
}

#color2:checked {
  background-color: #ffff00;
  box-shadow: 0 0 6em lighten(#ffff00, 10%);
}
input #color2:hover {
  animation: blink2 1.1s step-end infinite;
}

input #color3 {
  background-color: darken (#00ff00, 15%);
}

#color3:checked {
  background-color: #00ff00;
  box-shadow: 0 0 6em lighten(#00ff00, 10%);
}
input #color3:hover {
  animation: blink3 1.1s step-end infinite;
}

@keyframes blink1 {
  0% {
    background-color: #ff0000;
    box-shadow: 0 0 6em lighten(#ff0000, 10%);
  }

  50% {
    background-color: darken(#ff0000, 15%);
    box-shadow: 0 0 0em transparent;
  }
}

@keyframes blink2 {
  0% {
    background-color: #ffff00;
    box-shadow: 0 0 6em lighten(#ffff00, 10%);
  }

  50% {
    background-color: darken(#ffff00, 15%);
    box-shadow: 0 0 0em transparent;
  }
}

@keyframes blink3 {
  0% {
    background-color: #00ff00;
    box-shadow: 0 0 6em lighten(#00ff00, 10%);
  }

  50% {
    background-color: darken(#00ff00, 15%);
    box-shadow: 0 0 0em transparent;
  }
}

* {
  user-select: none;
  outline: none;
}
</style>
