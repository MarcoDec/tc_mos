import './style/app.scss'
import './style/fontawesome'
import 'bootstrap'
import App from './components/App.vue'
import AppBtn from './components/AppBtn'
import AppContainer from './components/AppContainer'
import AppDropdownItem from './components/nav/dropdown/AppDropdownItem.vue'
import AppForm from './components/form/AppForm.vue'
import AppInputGuesser from './components/form/field/input/AppInputGuesser.vue'
import AppMultiselect from './components/form/field/input/select/AppMultiselect.vue'
import AppOptions from './components/form/field/input/select/AppOptions.vue'
import AppOverlay from './components/AppOverlay'
import AppRouterLink from './components/nav/link/AppRouterLink.vue'
import AppSuspense from './components/AppSuspense.vue'
import AppTableFormField from './components/table/AppTableFormField.vue'
import AppTableHeaderForm from './components/table/head/AppTableHeaderForm.vue'
import AppTableItemField from './components/table/body/read/AppTableItemField.vue'
import AppTreeLabel from './components/tree/AppTreeLabel.vue'
import AppTreeNodes from './components/tree/AppTreeNodes.vue'
import Fa from './components/Fa'
import {createApp} from 'vue'
import pinia from './stores'
import router from './router'
import useUser from './stores/security'

const app = createApp(App)
    .component('AppBtn', AppBtn)
    .component('AppContainer', AppContainer)
    .component('AppDropdownItem', AppDropdownItem)
    .component('AppForm', AppForm)
    .component('AppInputGuesser', AppInputGuesser)
    .component('AppMultiselect', AppMultiselect)
    .component('AppOverlay', AppOverlay)
    .component('AppOptions', AppOptions)
    .component('AppRouterLink', AppRouterLink)
    .component('AppSuspense', AppSuspense)
    .component('AppTableFormField', AppTableFormField)
    .component('AppTableHeaderForm', AppTableHeaderForm)
    .component('AppTableItemField', AppTableItemField)
    .component('AppTreeLabel', AppTreeLabel)
    .component('AppTreeNodes', AppTreeNodes)
    .component('Fa', Fa)
    .use(pinia)
useUser().fetch().then(() => app.use(router).mount('#vue'))
