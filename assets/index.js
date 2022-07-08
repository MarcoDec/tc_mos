import './app.scss'
import './fortawesome'
import App from './App'
import AppBtn from './components/AppBtn'
import AppCard from './components/AppCard'
import AppCol from './components/layout/AppCol'
import AppContainer from './components/layout/AppContainer'
import AppDropdownItem from './components/nav/AppDropdownItem'
import AppForm from './components/form/AppForm'
import AppFormField from './components/form/field/AppFormField.vue'
import AppFormFieldset from './components/form/field/AppFormFieldset.vue'
import AppFormGroup from './components/form/field/AppFormGroup'
import AppFormTabs from './components/form/field/AppFormTabs.vue'
import AppInput from './components/form/field/input/AppInput'
import AppInputGuesser from './components/form/field/input/AppInputGuesser'
import AppLabel from './components/form/field/AppLabel'
import AppModal from './components/modal/AppModal.vue'
import AppOverlay from './components/AppOverlay'
import AppPaginationItem from './components/table/pagination/AppPaginationItem'
import AppRadio from './components/form/field/input/AppRadio'
import AppRadioGroup from './components/form/field/input/AppRadioGroup.vue'
import AppRouterLink from './components/nav/AppRouterLink'
import AppRow from './components/layout/AppRow'
import AppShowGuiCard from './components/gui/AppShowGuiCard.vue'
import AppTab from './components/tabs/AppTab.vue'
import AppTableFormField from './components/table/AppTableFormField'
import AppTableHeaderForm from './components/table/head/AppTableHeaderForm'
import AppTableItemField from './components/table/body/AppTableItemField.vue'
import AppTabs from './components/tabs/AppTabs.vue'
import AppTrafficLight from './components/form/field/input/AppTrafficLight.vue'
import AppTreeLabel from './components/tree/AppTreeLabel'
import CountryFlag from 'vue-country-flag-next'
import Fa from './components/Fa'
import {createApp} from 'vue'
import {createPinia} from 'pinia'
import router from './router'
import useUserStore from './stores/hr/employee/user'

const app = createApp(App)
    .component('AppBtn', AppBtn)
    .component('AppCard', AppCard)
    .component('AppCol', AppCol)
    .component('AppRow', AppRow)
    .component('AppContainer', AppContainer)
    .component('AppDropdownItem', AppDropdownItem)
    .component('AppForm', AppForm)
    .component('AppFormFieldset', AppFormFieldset)
    .component('AppFormField', AppFormField)
    .component('AppFormTabs', AppFormTabs)
    .component('AppFormGroup', AppFormGroup)
    .component('AppInput', AppInput)
    .component('AppInputGuesser', AppInputGuesser)
    .component('AppLabel', AppLabel)
    .component('AppModal', AppModal)
    .component('AppOverlay', AppOverlay)
    .component('AppPaginationItem', AppPaginationItem)
    .component('AppRouterLink', AppRouterLink)
    .component('AppShowGuiCard', AppShowGuiCard)
    .component('AppTab', AppTab)
    .component('AppRadioGroup', AppRadioGroup)
    .component('AppRadio', AppRadio)
    .component('AppTableFormField', AppTableFormField)
    .component('AppTableHeaderForm', AppTableHeaderForm)
    .component('AppTableItemField', AppTableItemField)
    .component('AppTabs', AppTabs)
    .component('AppTrafficLight', AppTrafficLight)
    .component('AppTreeLabel', AppTreeLabel)
    .component('CountryFlag', CountryFlag)
    .component('Fa', Fa)
    .use(createPinia())

async function fetchUser() {
    await useUserStore().fetch()
}

fetchUser().then(() => app.use(router).mount('#vue'))
