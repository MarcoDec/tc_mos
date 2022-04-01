<script lang="ts" setup></script>

<template>
  <div>
    <ul>
      <li>
        Achats
        <ul>
          <li>
            <router-link to="/supplier/show">Fournisseur</router-link>
          </li>
          <li><router-link to="/component/show">Composants</router-link></li>
        </ul>
      </li>
      <li>
        Ventes
        <ul>
          <li>
            <router-link to="/customer/show">Client</router-link>
          </li>
          <li>
            <router-link to="/product/show">Produits</router-link>
          </li>
        </ul>
      </li>
      <li>
        Direction
        <ul>
          <li>
            <router-link to="/company/show">Compagnie</router-link>
          </li>
        </ul>
      </li>
      <li>
        Projet
        <ul>
          <li>
            <router-link to="/product/show">Produits</router-link>
          </li>
        </ul>
      </li>
      <li>
        RH
        <ul>
          <li>
            <router-link to="/employee/show">Employées</router-link>
          </li>
        </ul>
      </li>
    </ul>
  </div>
</template>
