<script lang="ts" setup>
import {defineProps} from 'vue'

defineProps<{ href: string, title: string }>()
</script>

<template>
  <a :href="href" class="navbar-brand">
    {{ title }}
  </a>

  <!--<div class="dropdown">
    <button class="dropbtn">
      <Fa icon="sitemap"/>
        Message
      <svg viewBox="0 0 1030 638" width="10">
        <path d="M1017 68L541 626q-11 12-26 12t-26-12L13 68Q-3 49 6 24.5T39 0h952q24 0 33 24.5t-7 43.5z" fill="#FFF"></path>
      </svg>
    </button>

    <div class="dropdown-content">
      <router-link class="drop" to="/Currency/list"> <Fa class="me-2" icon="euro-sign"/>Device</router-link>
    </div>
  </div>-->

  <nav class="navbar navbar-expand-lg navbar-light bg-drak">
    <div class="container">
      <div class="dropdown">
        <button class="dropbtn">
          <Fa class="icon-notif" icon="bell"/>
        </button>
        <div class="collapse drop navbar-collapse" id="navbarSupportedContent">
          <ul class="navbar-nav ml-auto">
            <li class="nav-item dropdown notification-ui show">
              <a class="nav-link dropdown-toggle notification-ui_icon" href="#" id="navbarDropdown" role="button"
                 data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="unread-notification">22</span>
              </a>
              <div class="dropdown-content">

              <div class="dropdown-menu notification-ui_dd show" aria-labelledby="navbarDropdown">
                <div class="notification-ui_dd-header">
                  <h3 class="text-center">Notification</h3>
                </div>
                <div class="notification-ui_dd-content">

                  <div class="notification-list notification-list--unread">
                    <div class="notification-list_img">
                      <img src="https://i.imgur.com/zYxDCQT.jpg" alt="user">
                    </div>
                    <div class="notification-list_detail">
                      <p><b>John Doe</b> reacted to your post</p>
                      <p><small>10 mins ago</small></p>
                    </div>
                    <div class="notification-list_feature-img">
                      <img src="https://i.imgur.com/AbZqFnR.jpg" alt="Feature image">
                    </div>
                  </div>

                  <div class="notification-list notification-list--unread">
                    <div class="notification-list_img">
                      <img src="https://i.imgur.com/zYxDCQT.jpg" alt="user">
                    </div>
                    <div class="notification-list_detail">
                      <p><b>John Doe</b> reacted to your post</p>
                      <p><small>10 mins ago</small></p>
                    </div>
                    <div class="notification-list_feature-img">
                      <img src="https://i.imgur.com/AbZqFnR.jpg" alt="Feature image">
                    </div>
                  </div>


                  <div class="notification-list notification-list--unread">
                    <div class="notification-list_img">
                      <img src="https://i.imgur.com/zYxDCQT.jpg" alt="user">
                    </div>
                    <div class="notification-list_detail">
                      <p><b>John Doe</b> reacted to your post</p>
                      <p><small>10 mins ago</small></p>
                    </div>
                    <div class="notification-list_feature-img">
                      <img src="https://i.imgur.com/AbZqFnR.jpg" alt="Feature image">
                    </div>
                  </div>
                  <div class="notification-list notification-list--unread">
                    <div class="notification-list_img">
                      <img src="https://i.imgur.com/zYxDCQT.jpg" alt="user">
                    </div>
                    <div class="notification-list_detail">
                      <p><b>John Doe</b> reacted to your post</p>
                      <p><small>10 mins ago</small></p>
                    </div>
                    <div class="notification-list_feature-img">
                      <img src="https://i.imgur.com/AbZqFnR.jpg" alt="Feature image">
                    </div>
                  </div>
                  <div class="notification-list notification-list--unread">
                    <div class="notification-list_img">
                      <img src="https://i.imgur.com/zYxDCQT.jpg" alt="user">
                    </div>
                    <div class="notification-list_detail">
                      <p><b>John Doe</b> reacted to your post</p>
                      <p><small>10 mins ago</small></p>
                    </div>
                    <div class="notification-list_feature-img">
                      <img src="https://i.imgur.com/AbZqFnR.jpg" alt="Feature image">
                    </div>
                  </div>
                  <div class="notification-list notification-list--unread">
                    <div class="notification-list_img">
                      <img src="https://i.imgur.com/zYxDCQT.jpg" alt="user">
                    </div>
                    <div class="notification-list_detail">
                      <p><b>John Doe</b> reacted to your post</p>
                      <p><small>10 mins ago</small></p>
                    </div>
                    <div class="notification-list_feature-img">
                      <img src="https://i.imgur.com/AbZqFnR.jpg" alt="Feature image">
                    </div>
                  </div>

                  <div class="notification-list notification-list--unread">
                    <div class="notification-list_img">
                      <img src="https://i.imgur.com/w4Mp4ny.jpg" alt="user">
                    </div>
                    <div class="notification-list_detail">
                      <p><b>Richard Miles</b> reacted to your post</p>
                      <p><small>1 day ago</small></p>
                    </div>
                    <div class="notification-list_feature-img">
                      <img src="https://i.imgur.com/AbZqFnR.jpg" alt="Feature image">
                    </div>
                  </div>
                  <div class="notification-list">
                    <div class="notification-list_img">
                      <img src="https://i.imgur.com/ltXdE4K.jpg" alt="user">
                    </div>
                    <div class="notification-list_detail">
                      <p><b>Brian Cumin</b> reacted to your post</p>
                      <p><small>1 day ago</small></p>
                    </div>
                    <div class="notification-list_feature-img">
                      <img src="https://i.imgur.com/bpBpAlH.jpg" alt="Feature image">
                    </div>
                  </div>
                  <div class="notification-list">
                    <div class="notification-list_img">
                      <img src="https://i.imgur.com/CtAQDCP.jpg" alt="user">
                    </div>
                    <div class="notification-list_detail">
                      <p><b>Lance Bogrol</b> reacted to your post</p>
                      <p><small>1 day ago</small></p>
                    </div>
                    <div class="notification-list_feature-img">
                      <img src="https://i.imgur.com/iIhftMJ.jpg" alt="Feature image">
                    </div>
                  </div>
                </div>

              </div>
              </div>
            </li>
          </ul>
        </div>

      </div>
    </div>

  </nav>


</template>

<style scoped>

.icon-notif {
  color: white;
}

.navbar-expand-lg .navbar-collapse {
  display: flex !important;
  flex-basis: auto;
}

body {
  font-family: "Roboto", sans-serif;
  background: #E6E9ED;
  min-height: 100vh;
  position: relative;
}

.notification-ui a:after {
  display: none;
}

.notification-ui_icon {
  position: relative;
}

.notification-ui_icon .unread-notification {
  display: inline-block;
  height: 18px;
  width: 18px;
  border-radius: 7px;
  background-color: red;
  position: absolute;
  top: -46px;
  font-size: 12px;
  text-align: center;
  right: 0px;
  font-weight: bold;
}

@media (min-width: 900px) {
  .notification-ui_icon .unread-notification {
    left: 30px;
  }
}

@media (max-width: 1400px) {
  .notification-ui_icon .unread-notification {
    left: 30px;
  }
}

.notification-ui_dd {
  padding: 0;
  border-radius: 10px;
  -webkit-box-shadow: 0 5px 20px -3px rgba(0, 0, 0, 0.16);
  box-shadow: 0 5px 20px -3px rgba(0, 0, 0, 0.16);
  border: 0;
  max-width: 400px;
}

@media (min-width: 700px) {
  .notification-ui_dd {
    min-width: 400px;
    position: absolute;
    left: -392px;
    top: 70px;
  }
}

.notification-ui_dd:after {
  content: "";
  position: absolute;
  top: -30px;
  left: calc(50% - 7px);
  border-top: 15px solid transparent;
  border-right: 15px solid transparent;
  border-bottom: 15px solid #fff;
  border-left: 15px solid transparent;
}

.notification-ui_dd .notification-ui_dd-header {
  border-bottom: 1px solid #ddd;
  padding: 15px;
}

.notification-ui_dd .notification-ui_dd-header h3 {
  margin-bottom: 0;
}

.notification-ui_dd .notification-ui_dd-content {
  max-height: 500px;
  overflow: auto;
}

.notification-list {
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  -webkit-box-pack: justify;
  -ms-flex-pack: justify;
  justify-content: space-between;
  padding: 20px 0;
  margin: 5px 0px;
  border-bottom: 1px solid #ddd;
  background: #adb5bd73;
  cursor: pointer;
}

.notification-list--unread {
  position: relative;
}

.notification-list--unread:before {
  content: "";
  position: absolute;
  top: 0;
  left: -25px;
  height: calc(100% + 1px);
  border-left: 2px solid #29B6F6;
}

.notification-list .notification-list_img img {
  height: 48px;
  width: 48px;
  border-radius: 50px;
  margin-right: 20px;
}

.notification-list .notification-list_detail p {
  margin-bottom: 5px;
  line-height: 1.2;
}

.notification-list .notification-list_feature-img img {
  height: 48px;
  width: 48px;
  border-radius: 5px;
  margin-left: 20px;
}

.dropbtn {
  background-color: #222;
  color: white;
  padding: 16px;
  font-size: 16px;
  border: none;
  cursor: pointer;
}

.dropdown {
  position: relative;
  display: inline-block;
}

.dropdown-content {
  display: block;
  position: absolute;
  background-color: #f9f9f9;
  min-width: 160px;
  box-shadow: 0px 8px 16px 0px rgba(0, 0, 0, 0.2);
}

.dropdown-content a {
  color: black;
  padding: 12px 16px;
  text-decoration: none;
  display: block;
}

.dropdown-content a:hover {
  background-color: #222;
  color: #f1f1f1;
}

.dropdown:hover .dropdown-content {
  display: block;
}

.dropdown:hover .dropbtn {
  background-color: #222;
}



/*.dropdown {
  display:inline-block;
  margin-left:20px;
  padding:10px;
}


.glyphicon-bell {

  font-size:1.5rem;
}

.notifications {
  min-width:420px;
}

.notifications-wrapper {
  overflow:auto;
  max-height:250px;
}

.menu-title {
  color:#ff7788;
  font-size:1.5rem;
  display:inline-block;
}

.glyphicon-circle-arrow-right {
  margin-left:10px;
}


.notification-heading, .notification-footer  {
  padding:2px 10px;
}


.dropdown-menu {
  margin:5px 0;
}

.item-title {

  font-size:1.3rem;
  color:#000;

}

.notifications a.content {
  text-decoration:none;
  background:#ccc;

}

.notification-item {
  padding:10px;
  margin:5px;
  background:#ccc;
  border-radius:4px;
}

*/


</style>