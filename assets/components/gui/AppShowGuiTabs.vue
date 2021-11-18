<template>
  <div class="tab-header">
    <ul id="myTab" class="nav nav-tabs" role="tablist">
      <li class="nav-item" role="presentation">
        <a id="home-tab" class="active nav-link" data-bs-toggle="tab" href="#generaliste" role="tab"
           aria-controls="home" aria-selected="true">Généralistés</a>
      </li>
      <li class="nav-item" role="presentation">
        <a id="profile-tab" class="nav-link" data-bs-toggle="tab" href="#fichier" role="tab"
           aria-controls="profile" aria-selected="false">Fichiers</a>
      </li>
      <li class="nav-item" role="presentation">
        <a id="contact-tab" class="nav-link" data-bs-toggle="tab" href="#qualite" role="tab"
           aria-controls="contact" aria-selected="false">Qualité</a>
      </li>
      <li class="nav-item" role="presentation">
        <a id="achat-tab" class="nav-link" data-bs-toggle="tab" href="#achat" role="tab"
           aria-controls="contact" aria-selected="false">Achat/Logistique</a>
      </li>
      <li class="nav-item" role="presentation">
        <a id="comptabilite-tab" class="nav-link" data-bs-toggle="tab" href="#comptabilite" role="tab"
           aria-controls="contact" aria-selected="false">Comptabilité</a>
      </li>
    </ul>
  </div>
  <div id="myTabContent" class="tab-content">
    <div id="generaliste" class="active fade show tab-pane" role="tabpanel" aria-labelledby="home-tab">
      generaliste
    </div>
    <div id="fichier" class="fade tab-pane" role="tabpanel" aria-labelledby="profile-tab">
      fichier
    </div>
    <div id="qualite" class="fade tab-pane" role="tabpanel" aria-labelledby="contact-tab">
      qualite
    </div>
    <div id="achat" class="fade tab-pane" role="tabpanel" aria-labelledby="contact-tab">
      achat
    </div>
    <div id="comptabilite" class="fade tab-pane" role="tabpanel" aria-labelledby="contact-tab">
      comptabilite
    </div>
  </div>
</template>

<script lang="ts" setup>


</script>

<style scoped>
.nav-item {
  background: #007bff;
  width: 174px;
}

.nav-link {
  color: #007bff;
  background-color: white;
  text-align: center;
}

.nav-item[data-v-37a4cd2a] {
  background: none;
}

.nav-tabs .nav-link.active, .nav-tabs .nav-item.show .nav-link {
  color: white;
  background-color: #007bff;
  border-color: #dee2e6 #dee2e6 #fff;
}

.nav-tabs {
  border-bottom: 0px solid #dee2e6;
}

.tab-header {
  padding: 0.75rem 1.25rem;
  margin-bottom: 0;
  background-color: rgba(0, 0, 0, 0.03);
  border-bottom: 0px solid rgba(0, 0, 0, 0.125);
}

@media only screen and (min-width: 1100px) and (max-width: 1600px) {
  .nav-item {
    width: 150px;
  }
}
</style>
