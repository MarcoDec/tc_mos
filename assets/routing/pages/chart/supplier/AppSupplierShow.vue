<script lang="ts" setup>
import Vue3ChartJs from "@j-t-mcc/vue3-chartjs";
import AppChartTable from "../AppChartTable.vue";
import { defineProps } from "vue";

const props = defineProps({
  data: { required: true, type: Array },
  titleX: { required: true, type: String },
  titleY: { required: true, type: String },
});
const doughnutChart = {
  id: "doughnut",
  type: "doughnut",
  data: {
    labels: ["21/11/2017	", "21/12/2017	", "01/04/2018	", "11/05/2019"],
    datasets: [
      {
        backgroundColor: ["#41B883", "#E46651", "#00D8FF", "#DD1B16"],
        data: [40, 20, 80, 10],
      },
    ],
  },
};

const lineChart = {
  id: "chart",
  type: "mixed",
  data: {
    labels: [0.2, 0.3, 0.1, 0.4],
    datasets: [
      {
        id: "line",
        type: "scatter",
        label: "CA",
        data: [7, 9.6716, 9.7407, 9.7407, 9.7528, 9.7528],
        backgroundColor: [
          "rgba(255, 99, 132, 0.2)",
          "rgba(54, 162, 235, 0.2)",
          "rgba(255, 206, 86, 0.2)",
          "rgba(75, 192, 192, 0.2)",
          "rgba(153, 102, 255, 0.2)",
          "rgba(255, 159, 64, 0.2)",
        ],
        borderColor: [
          "rgba(255, 99, 132, 1)",
          "rgba(54, 162, 235, 1)",
          "rgba(255, 206, 86, 1)",
          "rgba(75, 192, 192, 1)",
          "rgba(153, 102, 255, 1)",
          "rgba(255, 159, 64, 1)",
        ],
        borderWidth: 1,
      },

    ],
  },
  options: {
    scales: {
      x: {
        type: "linear",
        position: "bottom",
        display: true,
        title: {
          display: true,
          text: "Semaine",
          color: "#911",
          font: {
            family: "Comic Sans MS",
            size: 20,
            weight: "bold",
            lineHeight: 1.2,
          },
          padding: { top: 10, left: 0, right: 0, bottom: 0 },
        },
      },
      y: {
        display: true,
        title: {
          display: true,
          text: "Prix",
          color: "#191",
          font: {
            family: "Times",
            size: 20,
            style: "normal",
            lineHeight: 1.2,
          },
          padding: { top: 30, left: 0, right: 0, bottom: 0 },
        },
      },
    },
    plugins: {
      zoom: {
        zoom: {
          wheel: {
            enabled: true,
          },
          pinch: {
            enabled: true,
          },
          mode: "x",
        },
      },
    },
  },
};

const livChart = {
  id: "chart",
  type: "mixed",
  data: {
    labels: [0.2, 0.3, 0.1, 0.4, 0.5, 1],
    datasets: [
      {
        id: "line",
        type: "scatter",
        label: "reçues",
        data: [7, 9.6716, 9.7407, 9.7407, 9.7528, 9.7528],
        backgroundColor: [
          "rgba(255, 99, 132, 0.2)",
          "rgba(54, 162, 235, 0.2)",
          "rgba(255, 206, 86, 0.2)",
          "rgba(75, 192, 192, 0.2)",
          "rgba(153, 102, 255, 0.2)",
          "rgba(255, 159, 64, 0.2)",
        ],
        borderColor: [
          "rgba(255, 99, 132, 1)",
          "rgba(54, 162, 235, 1)",
          "rgba(255, 206, 86, 1)",
          "rgba(75, 192, 192, 1)",
          "rgba(153, 102, 255, 1)",
          "rgba(255, 159, 64, 1)",
        ],
        borderWidth: 1,
      },
       {
        id: "line",
        type: "scatter",
        label: "souhaitees",
        data: [7, 9.6716, 9.7407, 9.7407, 9.7528, 9.7528],
        backgroundColor: [
          "rgba(255, 99, 132, 0.2)",
          "rgba(54, 162, 235, 0.2)",
          "rgba(255, 206, 86, 0.2)",
          "rgba(75, 192, 192, 0.2)",
          "rgba(153, 102, 255, 0.2)",
          "rgba(255, 159, 64, 0.2)",
        ],
        borderColor: [
          "rgba(255, 99, 132, 1)",
          "rgba(54, 162, 235, 1)",
          "rgba(255, 206, 86, 1)",
          "rgba(75, 192, 192, 1)",
          "rgba(153, 102, 255, 1)",
          "rgba(255, 159, 64, 1)",
        ],
        borderWidth: 1,
      },
      
    ],
  },
  options: {
    scales: {
      x: {
        type: "linear",
        position: "bottom",
        display: true,
        title: {
          display: true,
          text: "Semaine",
          color: "#911",
          font: {
            family: "Comic Sans MS",
            size: 20,
            weight: "bold",
            lineHeight: 1.2,
          },
          padding: { top: 10, left: 0, right: 0, bottom: 0 },
        },
      },
      y: {
        display: true,
        title: {
          display: true,
          text: "Prix",
          color: "#191",
          font: {
            family: "Times",
            size: 20,
            style: "normal",
            lineHeight: 1.2,
          },
          padding: { top: 30, left: 0, right: 0, bottom: 0 },
        },
      },
    },
    plugins: {
      zoom: {
        zoom: {
          wheel: {
            enabled: true,
          },
          pinch: {
            enabled: true,
          },
          mode: "x",
        },
      },
    },
  },
};

console.log("data--", lineChart.data.datasets);
</script>

<template>
  <div class="text-center bg-secondary text-white">
    <b>Livraisons attendues vs reçues</b>
  </div>
  <AppRow>
    <vue3-chart-js
      :id="livChart.id"
      :type="livChart.type"
      :data="livChart.data"
      :options="livChart.options"
    ></vue3-chart-js>
    <AppChartTable
      :data="livChart.data"
      :titleX="livChart.options.scales.x.title.text"
      :titleY="livChart.options.scales.y.title.text"
    />
  </AppRow>

  <div class="text-center bg-secondary text-white">
    <b>Chiffre d'Affaire</b>
  </div>

  <AppRow>
    <vue3-chart-js
      :id="lineChart.id"
      :type="lineChart.type"
      :data="lineChart.data"
      :options="lineChart.options"
    ></vue3-chart-js>
    <AppChartTable
      :data="lineChart.data"
      :titleX="lineChart.options.scales.x.title.text"
      :titleY="lineChart.options.scales.y.title.text"
    />
  </AppRow>

  <div class="text-center bg-secondary text-white">
    <b>Retard vs Avance livraisons</b>
  </div>

  <AppRow>
    <vue3-chart-js
      :id="lineChart.id"
      :type="lineChart.type"
      :data="lineChart.data"
      :options="lineChart.options"
    ></vue3-chart-js>
    <AppChartTable
      :data="lineChart.data"
      :titleX="lineChart.options.scales.x.title.text"
      :titleY="lineChart.options.scales.y.title.text"
    />
  </AppRow>
</template>
