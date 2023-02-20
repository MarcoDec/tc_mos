import {defineStore} from 'pinia'
import useTab from './tab'

export default function useTabs(id) {
    return defineStore(id, {
        actions: {
            dispose() {
                for (const tab of this.tabs)
                    tab.dispose()
                this.$dispose()
            },
            push(tab) {
                const tabStore = useTab(tab, this)
                this.tabs.push(tabStore)
                return tabStore
            },
            removeTab(removed) {
                this.tabs = this.tabs.filter(tab => tab.$id !== removed.$id)
            }
        },
        state: () => ({tabs: []})
    })()
}
