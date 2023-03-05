import './style/app.scss'
import './style/fontawesome'
import 'bootstrap'
import App from './components/App.vue'
import AppBtn from './components/AppBtn.vue'
import AppCard from './components/AppCard.vue'
import AppCol from './components/AppCol'
import AppContainer from './components/AppContainer'
import AppDropdownItem from './components/nav/dropdown/AppDropdownItem.vue'
import AppForm from './components/form/AppForm.vue'
import AppFormField from './components/form/field/AppFormField.vue'
import AppFormFieldset from './components/form/field/AppFormFieldset.vue'
import AppFormGenerator from './components/form/AppFormGenerator.vue'
import AppFormGroup from './components/form/field/AppFormGroup'
import AppFormTabs from './components/form/field/AppFormTabs.vue'
import AppInput from './components/form/field/input/AppInput.vue'
import AppInputGuesser from './components/form/field/input/AppInputGuesser.vue'
import AppLabel from './components/form/field/AppLabel.vue'
import AppModal from './components/modal/AppModal.vue'
import AppMultiselect from './components/form/field/input/select/AppMultiselect.vue'
import AppOptions from './components/form/field/input/select/AppOptions.vue'
import AppOverlay from './components/AppOverlay'
import AppPaginationItem from './components/table/pagination/AppPaginationItem'
import AppRadio from './components/form/field/input/AppRadio'
import AppRadioGroup from './components/form/field/input/AppRadioGroup.vue'
import AppRouterLink from './components/nav/link/AppRouterLink.vue'
import AppRow from './components/AppRow'
import AppShowGuiCard from './components/gui/AppShowGuiCard.vue'
import AppSuspense from './components/AppSuspense.vue'
import AppTab from './components/tab/AppTab.vue'
import AppTableFormField from './components/table/AppTableFormField.vue'
import AppTableHeaderForm from './components/table/head/AppTableHeaderForm.vue'
import AppTableItemField from './components/table/body/read/AppTableItemField.vue'
import AppTabs from './components/tab/AppTabs.vue'
import AppTrafficLight from './components/form/field/input/AppTrafficLight.vue'
import AppTreeForm from './components/tree/card/form/AppTreeForm.vue'
import AppTreeLabel from './components/tree/node/AppTreeLabel.vue'
import AppTreeNodes from './components/tree/node/AppTreeNodes.vue'
import AppTreePage from './components/pages/tree/AppTreePage.vue'
import CountryFlag from 'vue-country-flag-next'
import Fa from './components/Fa'
import {createApp} from 'vue'
import pinia from './stores'
import router from './router'
import useUser from './stores/security'

const app = createApp(App)
    .component('AppBtn', AppBtn)
    .component('AppCard', AppCard)
    .component('AppCol', AppCol)
    .component('AppContainer', AppContainer)
    .component('AppDropdownItem', AppDropdownItem)
    .component('AppForm', AppForm)
    .component('AppFormFieldset', AppFormFieldset)
    .component('AppFormField', AppFormField)
    .component('AppFormGenerator', AppFormGenerator)
    .component('AppFormTabs', AppFormTabs)
    .component('AppFormGroup', AppFormGroup)
    .component('AppInput', AppInput)
    .component('AppInputGuesser', AppInputGuesser)
    .component('AppLabel', AppLabel)
    .component('AppModal', AppModal)
    .component('AppMultiselect', AppMultiselect)
    .component('AppOverlay', AppOverlay)
    .component('AppOptions', AppOptions)
    .component('AppPaginationItem', AppPaginationItem)
    .component('AppRadioGroup', AppRadioGroup)
    .component('AppRadio', AppRadio)
    .component('AppRouterLink', AppRouterLink)
    .component('AppRow', AppRow)
    .component('AppShowGuiCard', AppShowGuiCard)
    .component('AppSuspense', AppSuspense)
    .component('AppTab', AppTab)
    .component('AppTableFormField', AppTableFormField)
    .component('AppTableHeaderForm', AppTableHeaderForm)
    .component('AppTableItemField', AppTableItemField)
    .component('AppTabs', AppTabs)
    .component('AppTrafficLight', AppTrafficLight)
    .component('AppTreeForm', AppTreeForm)
    .component('AppTreeLabel', AppTreeLabel)
    .component('AppTreeNodes', AppTreeNodes)
    .component('AppTreePage', AppTreePage)
    .component('CountryFlag', CountryFlag)
    .component('Fa', Fa)
    .use(pinia)
useUser().fetch().then(() => app.use(router).mount('#vue'))
