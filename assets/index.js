import './style/app.scss'
import './style/fontawesome'
import 'bootstrap'
import App from './components/App.vue'
import AppBtn from './components/AppBtn.vue'
import AppCard from './components/AppCard.vue'
import AppContainer from './components/AppContainer'
import AppDropdownItem from './components/nav/dropdown/AppDropdownItem.vue'
import AppForm from './components/form/AppForm.vue'
import AppFormGenerator from './components/form/AppFormGenerator.vue'
import AppInput from './components/form/field/input/AppInput.vue'
import AppInputGuesser from './components/form/field/input/AppInputGuesser.vue'
import AppMultiselect from './components/form/field/input/select/AppMultiselect.vue'
import AppOptions from './components/form/field/input/select/AppOptions.vue'
import AppOverlay from './components/AppOverlay'
import AppRouterLink from './components/nav/link/AppRouterLink.vue'
import AppSuspense from './components/AppSuspense.vue'
import AppTableFormField from './components/table/AppTableFormField.vue'
import AppTableHeaderForm from './components/table/head/AppTableHeaderForm.vue'
import AppTableItemField from './components/table/body/read/AppTableItemField.vue'
import AppTreeForm from './components/tree/card/form/AppTreeForm.vue'
import AppTreeLabel from './components/tree/node/AppTreeLabel.vue'
import AppTreeNodes from './components/tree/node/AppTreeNodes.vue'
import AppTreePage from './components/pages/tree/AppTreePage.vue'
import Fa from './components/Fa'
import {createApp} from 'vue'
import pinia from './stores'
import router from './router'
import useUser from './stores/security'

const app = createApp(App)
    .component('AppBtn', AppBtn)
    .component('AppCard', AppCard)
    .component('AppContainer', AppContainer)
    .component('AppDropdownItem', AppDropdownItem)
    .component('AppForm', AppForm)
    .component('AppFormGenerator', AppFormGenerator)
    .component('AppInput', AppInput)
    .component('AppInputGuesser', AppInputGuesser)
    .component('AppMultiselect', AppMultiselect)
    .component('AppOverlay', AppOverlay)
    .component('AppOptions', AppOptions)
    .component('AppRouterLink', AppRouterLink)
    .component('AppSuspense', AppSuspense)
    .component('AppTableFormField', AppTableFormField)
    .component('AppTableHeaderForm', AppTableHeaderForm)
    .component('AppTableItemField', AppTableItemField)
    .component('AppTreeForm', AppTreeForm)
    .component('AppTreeLabel', AppTreeLabel)
    .component('AppTreeNodes', AppTreeNodes)
    .component('AppTreePage', AppTreePage)
    .component('Fa', Fa)
    .use(pinia)
useUser().fetch().then(() => app.use(router).mount('#vue'))
