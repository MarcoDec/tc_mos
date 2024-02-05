export const actions = {
  async load({ commit }) {
    const  = {
      productChartsData: {
        1: {
          labels: [0.2, 0.3, 0.1, 0.4],
          stockProgress: [9.4185, 9.6716, 9.7407, 9.7407, 9.7528, 9.7528],
          stockMinimum: [9.7407, 9.7407, 9.7407, 9.7407, 9.7407, 9.7407],
        },
        2: {
          labels: [
            "21/11/2017",
            "21/12/2017",
            "01/04/2018",
            "11/05/2019",
            "11/06/2019",
          ],
          stockProgress: [9.4185, 9.6716, 9.7407, 9.7407, 9.7528, 9.7528],
          stockMinimum: [9.7407, 9.7407, 9.7407, 9.7407, 9.7407, 9.7407],
        },
        3: {
          labels: [0.23, 0.3, 0.1, 0.4],
          stockProgress: [9.4185, 9.6716, 9.7407, 9.7407, 9.7528, 9.7528],
          stockMinimum: [9.7407, 9.7407, 9.7407, 9.7407, 9.7407, 9.7407],
        },
        4: {
          labels: [0.24, 0.3, 0.1, 0.4],
          stockProgress: [9.4185, 9.6716, 9.7407, 9.7407, 9.7528, 9.7528],
          stockMinimum: [9.7407, 9.7407, 9.7407, 9.7407, 9.7407, 9.7407],
        },
        5: {
          labels: [0.25, 0.3, 0.1, 0.4],
          stockProgress: [9.4185, 9.6716, 9.7407, 9.7407, 9.7528, 9.7528],
          stockMinimum: [9.7407, 9.7407, 9.7407, 9.7407, 9.7407, 9.7407],
        },
        6: {
          labels: [0.26, 0.3, 0.1, 0.4],
          stockProgress: [9.4185, 9.6716, 9.7407, 9.7407, 9.7528, 9.7528],
          stockMinimum: [9.7407, 9.7407, 9.7407, 9.7407, 9.7407, 9.7407],
        },
      },
      componentChartsData: {
        1: {
          labels: [0.2, 0.3, 0.1, 0.4],
          stockProgress: [9.4185, 9.6716, 9.7407, 9.7407, 9.7528, 9.7528],
          stockMinimum: [9.7407, 9.7407, 9.7407, 9.7407, 9.7407, 9.7407],
        },
        2: {
          labels: [
            "21/11/2017",
            "21/12/2017",
            "01/04/2018",
            "11/05/2019",
            "11/06/2019",
          ],
          stockProgress: [9.4185, 9.6716, 9.7407, 9.7407, 9.7528, 9.7528],
          stockMinimum: [9.7407, 9.7407, 9.7407, 9.7407, 9.7407, 9.7407],
        },
        3: {
          labels: [0.23, 0.3, 0.1, 0.4],
          stockProgress: [9.4185, 9.6716, 9.7407, 9.7407, 9.7528, 9.7528],
          stockMinimum: [9.7407, 9.7407, 9.7407, 9.7407, 9.7407, 9.7407],
        },
        4: {
          labels: [0.24, 0.3, 0.1, 0.4],
          stockProgress: [9.4185, 9.6716, 9.7407, 9.7407, 9.7528, 9.7528],
          stockMinimum: [9.7407, 9.7407, 9.7407, 9.7407, 9.7407, 9.7407],
        },
        5: {
          labels: [0.25, 0.3, 0.1, 0.4],
          stockProgress: [9.4185, 9.6716, 9.7407, 9.7407, 9.7528, 9.7528],
          stockMinimum: [9.7407, 9.7407, 9.7407, 9.7407, 9.7407, 9.7407],
        },
      },
      customers: {
        1: {
          id: 1,
          products: [1, 2, 3, 4],
          society: 1,
        },
      },
      suppliers: {},
      products: {
        1: {
          minStock: 55,
          productRef: "4444",
          productDesg: "d1",
          family: [1, 2, 3],
          components: [1, 2],
          stockDefault: {
            1: { date: "2022-05-02" },
            2: { date: "2022-02-08" },
            3: { date: "2022-03-08" },
          },
          newOF: {
            1: { date: "2022-05-12", quantity: "200" },
            2: { date: "2022-02-08", quantity: "100" },
          },
          productStock: 1000,
          productToManufactring: 1000,
        },
        2: {
          minStock: 75,
          productRef: "12154",
          productDesg: "d2",
          family: [1, 2, 3],
          components: [1, 2],
          stockDefault: {
            1: { date: "2020-05-12" },
            2: { date: "2022-02-08" },
          },
          newOF: {
            1: { date: "2020-05-12", quantity: "200" },
            2: { date: "2022-02-08", quantity: "500" },
          },
          productStock: 1000,
          productToManufactring: 1000,
        },
        3: {
          minStock: 20,
          productRef: "87966",
          productDesg: "d3",
          family: [1, 2, 3],
          components: [1, 2],
          stockDefault: {
            1: { date: "2020-05-12" },
            2: { date: "2022-02-08" },
          },
          newOF: {
            1: { date: "2022-05-12", quantity: "200" },
            2: { date: "2022-02-08", quantity: "100" },
          },
          productStock: 1000,
          productToManufactring: 1000,
        },
        4: {
          minStock: 100,
          productRef: "22222",
          productDesg: "d4",
          family: [1, 2, 3],
          components: [1, 2],
          stockDefault: {
            1: { date: "2019-01-12" },
            2: { date: "2022-02-08" },
          },
          newOF: {
            1: { date: "2022-05-12", quantity: 200 },
            2: { date: "2022-02-08", quantity: 100 },
          },
          productStock: 1000,
          productToManufactring: 1000,
        },
        5: {
          minStock: 50,
          productRef: "4444",
          productDesg: "d1",
          family: [1, 2, 3],
          components: [1, 2],
          stockDefault: {
            1: { date: "2022-07-12" },
            2: { date: "2022-02-08" },
          },
          newOF: {
            1: { date: "2022-05-12", quantity: 200 },
            2: { date: "2022-02-08", quantity: 100 },
          },
          productStock: 1000,
          productToManufactring: 1000,
        },
        6: {
          minStock: 200,
          productRef: "2008",
          productDesg: "d1",
          family: [1, 2, 3],
          components: [1, 2],
          stockDefault: {
            1: { date: "2010-05-12" },
            2: { date: "2011-02-08" },
          },
          newOF: {
            1: { date: "2022-05-12", quantity: 200 },
            2: { date: "2022-02-08", quantity: 100 },
          },
          productStock: 1000,
          productToManufactring: 1000,
        },
      },
      productFamilies: {
        1: { pathName: "path1", familyName: "prodFamil1" },
        2: { pathName: "path2", familyName: "prodFamil1" },
        3: { pathName: "path3", familyName: "prodFamil1" },
      },
      components: {
        1: {
          ref: 1,
          family: [1, 2, 3],
          componentStockDefaults: {
            1: { date: "2022-07-12" },
            2: { date: "2022-02-08" },
          },
          newSupplierOrder: {
            1: { date: "2022-05-12", quantity: 200 },
            2: { date: "2022-02-08", quantity: 100 },
          },
        },
        2: {
        ref: 2,
          family: [1, 2, 3],
          componentStockDefaults: {
            1: { date: "2022-07-12" },
            2: { date: "2022-02-08" },
          },
          newSupplierOrder: {
            1: { date: "2022-05-12", quantity: 200 },
            2: { date: "2022-02-08", quantity: 100 },
          },
        },
      },
    };
    commit("", );

  },
  async show({ commit, getters }, infinite) {
    commit("show");
    //commit("showCom");
    if (getters.has) {
      infinite.loaded();
    } else {
      infinite.complete();
    }
  },
  async showCom({ commit, getters }, infinite) {
    commit("showCom");
    if (getters.hasCom) {
      infinite.loaded();
    } else {
      infinite.complete();
    }
  },
};
