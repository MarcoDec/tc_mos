import { defineStore } from "pinia";

export default function generatePrice(prices) {
  return defineStore(`prices/${prices.id}`, {
    actions: {
      blur() {
        this.opened = false;
        this.selected = false;
      },
      dispose() {
        this.$reset();
        this.$dispose();
      },
      focus() {
        this.root.blur();
        this.selected = true;
        this.open();
      },
    },
    getters: {
      rowspan: (state) => state.prices.length + 1,
    },
    state: () => ({ ...prices }),
  })();
}
