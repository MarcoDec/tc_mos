import './app.scss'
import './fortawesome'
import App from './App'
import AppBtn from './components/AppBtn'
import AppCard from './components/AppCard'
import AppCardShow from './components/AppCardShow.vue'
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
import AppTable from './components/table/AppTable'
import AppTableFormField from './components/table/AppTableFormField'
import AppTableHeaderForm from './components/table/head/AppTableHeaderForm'
import AppTableItemField from './components/table/body/AppTableItemField.vue'
import AppTablePage from './router/pages/AppTablePage'
import AppTabs from './components/tabs/AppTabs.vue'
import AppTreeLabel from './components/tree/AppTreeLabel'
import Fa from './components/Fa'
import {createApp} from 'vue'
import {createPinia} from 'pinia'
import router from './router'
import useUserStore from './stores/hr/employee/user'

const app = createApp(App)
    .component('AppBtn', AppBtn)
    .component('AppCard', AppCard)
    .component('AppCardShow', AppCardShow)
    .component('AppContainer', AppContainer)
    .component('AppCol', AppCol)
    .component('AppRow', AppRow)
    .component('AppDropdownItem', AppDropdownItem)
    .component('AppForm', AppForm)
    .component('AppInput', AppInput)
    .component('AppInputGuesser', AppInputGuesser)
    .component('AppModal', AppModal)
    .component('AppOverlay', AppOverlay)
    .component('AppPaginationItem', AppPaginationItem)
    .component('AppRouterLink', AppRouterLink)
    .component('AppShowGuiCard', AppShowGuiCard)
    .component('AppTab', AppTab)
    .component('AppTableFormField', AppTableFormField)
    .component('AppTableHeaderForm', AppTableHeaderForm)
    .component('AppTableItemField', AppTableItemField)
    .component('AppTable', AppTable)
    .component('AppTablePage', AppTablePage)
    .component('AppTabs', AppTabs)
    .component('AppTreeLabel', AppTreeLabel)
    .component('Fa', Fa)
    .use(createPinia())

async function fetchUser() {
    await useUserStore().fetch()
}

fetchUser().then(() => app.use(router).mount('#vue'))
