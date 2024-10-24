import {defineStore} from 'pinia'

export default function useTab(tab, parent) {
    return defineStore(`${parent.$id}/${tab.id}`, {
        actions: {
            dispose() {
                this.parent.removeTab(this)
                this.$dispose()
            }
        },
        getters: {
            activeCss: state => ({active: state.active}),
            css: state => ({active: state.active, show: state.active}),
            labelledby: state => `${state.id}-tab`,
            target: state => `#${state.id}`
        },
        state: () => ({...tab, parent})
    })()
}
