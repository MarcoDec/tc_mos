export const getters = {
  // eslint-disable-next-line  @typescript-eslint/no-unnecessary-condition
  hasNeeds: (state) => Object.values(state.needs).length > 0,
  hasNeedsCom: (state) => Object.values(state.needsComp).length > 0,
  hasDisplayed: (state) => Object.values(state.displayedComponent).length > 0,
  listDisplayed: (state) => Object.values(state.displayed),
  listDisplayedComp: (state) => Object.values(state.displayedComponent),
  chartsComp: (state) => (componentId) => state.initiale.componentChartsData[componentId],
  charts: (state) => (productId) => state.initiale.productChartsData[productId],
  normalizedChartComp: (state, computed) => (componentId) => {
    const data = computed.chartsComp(componentId);
    console.log("zzzzzz",data);
    return {
      id: "chart",
      type: "mixed",
      data: {
        labels:data.labels,
        datasets: [
          {
            id: "line",
            type: "line",
            label: "prixLine",
            data: data.stockProgress,
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
            id: "bar",
            type: "line",
            label: "prixBar",
            data:data.stockMinimum,
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
   
  
},
  normalizedChart: (state, computed) => (productId) => {
      const data = computed.charts(productId);
      return {
        id: "chart",
        type: "mixed",
        data: {
          labels:data.labels,
          datasets: [
            {
              id: "line",
              type: "line",
              label: "prixLine",
              data: data.stockProgress,
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
              id: "bar",
              type: "line",
              label: "prixBar",
              data:data.stockMinimum,
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
     
    
  },
};