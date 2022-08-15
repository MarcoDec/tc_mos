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
      rows: (state) => state.items.map((item) => item.rows).flat(1),
    },
    state: () => ({ items: [componentSupplier] }),
  })();
}
