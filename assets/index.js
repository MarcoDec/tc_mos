import './app.scss'
import './fortawesome'
import App from './App'
import AppBtn from './components/AppBtn'
import AppBtnSplit from './components/AppBtnSplit.vue'
import AppCard from './components/AppCard'
import AppCol from './components/layout/AppCol'
import AppContainer from './components/layout/AppContainer'
import AppDropdownItem from './components/nav/AppDropdownItem'
import AppForm from './components/form/AppForm'
import AppInput from './components/form/field/input/AppInput'
import AppInputGuesser from './components/form/field/input/AppInputGuesser'
import AppModal from './components/modal/AppModal.vue'
import AppOverlay from './components/AppOverlay'
import AppPaginationItem from './components/table/pagination/AppPaginationItem'
import AppRouterLink from './components/nav/AppRouterLink'
import AppRow from './components/layout/AppRow'
import AppShowGuiCard from './components/gui/AppShowGuiCard.vue'
import AppTab from './components/tabs/AppTab.vue'
import AppTableFormField from './components/table/AppTableFormField'
import AppTableHeaderForm from './components/table/head/AppTableHeaderForm'
import AppTableItemField from './components/table/body/AppTableItemField.vue'
import AppTabs from './components/tabs/AppTabs.vue'
import AppTreeLabel from './components/tree/AppTreeLabel'
import Fa from './components/Fa'
import {createApp} from 'vue'
import {createPinia} from 'pinia'
import router from './router'
import useUserStore from './stores/hr/employee/user'

const app = createApp(App)
    .component('AppBtn', AppBtn)
    .component('AppBtnSplit', AppBtnSplit)
    .component('AppCard', AppCard)
    .component('AppModal', AppModal)
    .component('AppCol', AppCol)
    .component('AppRow', AppRow)
    .component('AppContainer', AppContainer)
    .component('AppDropdownItem', AppDropdownItem)
    .component('AppForm', AppForm)
    .component('AppInput', AppInput)
    .component('AppInputGuesser', AppInputGuesser)
    .component('AppOverlay', AppOverlay)
    .component('AppPaginationItem', AppPaginationItem)
    .component('AppRouterLink', AppRouterLink)
    .component('AppShowGuiCard', AppShowGuiCard)
    .component('AppTab', AppTab)
    .component('AppTableFormField', AppTableFormField)
    .component('AppTableHeaderForm', AppTableHeaderForm)
    .component('AppTableItemField', AppTableItemField)
    .component('AppTabs', AppTabs)
    .component('AppTreeLabel', AppTreeLabel)
    .component('Fa', Fa)
    .use(createPinia())

async function fetchUser() {
    await useUserStore().fetch()
}

fetchUser().then(() => app.use(router).mount('#vue'))
