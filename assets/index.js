import './style/app.scss'
import './style/fontawesome'
import 'bootstrap'
import App from './components/App.vue'
import AppBtn from './components/AppBtn.vue'
import AppBtnJS from './components/AppBtnJS'
import AppCard from './components/AppCard.vue'
import AppCardJS from './components/AppCardJS'
import AppCardShow from './components/AppCardShow.vue'
import AppCardableTable from './components/bootstrap-5/app-cardable-collection-table/AppCardableTable.vue'
import AppCol from './components/layout/AppCol'
import AppContainer from './components/AppContainer'
import AppDropdownItem from './components/nav/dropdown/AppDropdownItem.vue'
import AppDropdownItemJS from './components/nav/dropdown/AppDropdownItemJS'
import AppForm from './components/form/AppForm.vue'
import AppFormCardable from './components/form-cardable/AppFormCardable'
import AppFormGenerator from './components/form/AppFormGenerator.vue'
import AppFormGroup from './components/form/field/AppFormGroup.vue'
import AppFormGroupJS from './components/form/field/AppFormGroupJS'
import AppFormJS from './components/form/AppFormJS'
import AppInput from './components/form/field/input/AppInput.vue'
import AppInputGuesser from './components/form/field/input/AppInputGuesser.vue'
import AppInputGuesserJS from './components/form/field/input/AppInputGuesserJS'
import AppInputJS from './components/form/field/input/AppInputJS'
import AppLabel from './components/form/field/AppLabel.vue'
import AppLabelJS from './components/form/field/AppLabelJS'
import AppModal from './components/modal/AppModal.vue'
import AppMultiselect from './components/form/field/input/select/AppMultiselect.vue'
import AppOptions from './components/form/field/input/select/AppOptions.vue'
//import AppOverlay from './components/AppOverlay'
import AppOverlayJS from './components/AppOverlayJS'
import AppPaginationItem from './components/table/pagination/AppPaginationItem'
import AppRouterLink from './components/nav/link/AppRouterLink.vue'
import AppRouterLinkJS from './components/nav/link/AppRouterLinkJS'
import AppRow from './components/layout/AppRow'
import AppShowGuiCard from './components/gui/AppShowGuiCard.vue'
import AppSuspense from './components/AppSuspense.vue'
import AppTab from './components/tab/AppTab.vue'
import AppTable from './components/table/AppTable.vue'
import AppTableAdd from './components/table/head/AppTableAdd.vue'
import AppTableAddJS from './components/table/head/AppTableAddJS'
import AppTableFields from './components/table/head/field/AppTableFields.vue'
import AppTableFieldsJS from './components/table/head/field/AppTableFieldsJS'
import AppTableFormField from './components/table/AppTableFormField.vue'
import AppTableFormFieldJS from './components/table/AppTableFormFieldJS'
import AppTableHeaderForm from './components/table/head/AppTableHeaderForm.vue'
import AppTableHeaderFormJS from './components/table/head/AppTableHeaderFormJS'
import AppTableHeaders from './components/table/head/AppTableHeaders.vue'
import AppTableHeadersJS from './components/table/head/AppTableHeadersJS'
import AppTableItem from './components/table/body/AppTableItem.vue'
import AppTableItemField from './components/table/body/read/AppTableItemField.vue'
import AppTableItemJS from './components/table/body/AppTableItemJS'
import AppTableItemUpdate from './components/table/body/update/AppTableItemUpdate.vue'
import AppTableItemUpdateField from './components/table/body/update/AppTableItemUpdateField.vue'
import AppTableItemUpdateFieldJS from './components/table/body/update/AppTableItemUpdateFieldJS'
import AppTableItemUpdateJS from './components/table/body/update/AppTableItemUpdateJS'
import AppTableItems from './components/table/body/AppTableItems.vue'
import AppTableItemsJS from './components/table/body/AppTableItemsJS'
import AppTableJS from './components/table/AppTableJS'
import AppTablePage from './components/pages/table/AppTablePage.vue'
import AppTableSearch from './components/table/head/AppTableSearch.vue'
import AppTableSearchJS from './components/table/head/AppTableSearchJS'
import AppTabs from './components/tab/AppTabs.vue'
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
    .component('AppBtnJS', AppBtnJS)
    .component('AppCard', AppCard)
    .component('AppCardJS', AppCardJS)
    .component('AppCardShow', AppCardShow)
    .component('AppContainer', AppContainer)
    .component('AppCol', AppCol)
    .component('AppRow', AppRow)
    .component('AppShowGuiCard', AppShowGuiCard)
    .component('AppCardableTable', AppCardableTable)
    .component('AppDropdownItem', AppDropdownItem)
    .component('AppDropdownItemJS', AppDropdownItemJS)
    .component('AppForm', AppForm)
    .component('AppFormJS', AppFormJS)
    .component('AppFormCardable', AppFormCardable)
    .component('AppFormGenerator', AppFormGenerator)
    .component('AppFormGroup', AppFormGroup)
    .component('AppFormGroupJS', AppFormGroupJS)
    .component('AppInput', AppInput)
    .component('AppInputJS', AppInputJS)
    .component('AppInputGuesser', AppInputGuesser)
    .component('AppInputGuesserJS', AppInputGuesserJS)
    .component('AppLabel', AppLabel)
    .component('AppLabelJS', AppLabelJS)
    .component('AppModal', AppModal)
    .component('AppMultiselect', AppMultiselect)
    .component('AppOverlay', AppOverlayJS)
    .component('AppPaginationItem', AppPaginationItem)
    .component('AppOverlay', AppOverlay)
    .component('AppOptions', AppOptions)
    .component('AppRouterLink', AppRouterLink)
    .component('AppRouterLinkJS', AppRouterLinkJS)
    .component('AppRow', AppRow)
    .component('AppSuspense', AppSuspense)
    .component('AppTab', AppTab)
    .component('AppTableFormField', AppTableFormField)
    .component('AppTableFormFieldJS', AppTableFormFieldJS)
    .component('AppTableHeaders', AppTableHeaders)
    .component('AppTableHeadersJS', AppTableHeadersJS)
    .component('AppTableHeaderForm', AppTableHeaderForm)
    .component('AppTableHeaderFormJS', AppTableHeaderFormJS)
    .component('AppTableItem', AppTableItem)
    .component('AppTableItemField', AppTableItemField)
    .component('AppTableItemJS', AppTableItemJS)
    .component('AppTableItemUpdate', AppTableItemUpdate)
    .component('AppTableItemUpdateJS', AppTableItemUpdateJS)
    .component('AppTableItems', AppTableItems)
    .component('AppTableItemsJS', AppTableItemsJS)
    .component('AppTableItemUpdateField', AppTableItemUpdateField)
    .component('AppTableItemUpdateFieldJS', AppTableItemUpdateFieldJS)
    .component('AppTable', AppTable)
    .component('AppTableAdd', AppTableAdd)
    .component('AppTableAddJS', AppTableAddJS)
    .component('AppTableFields', AppTableFields)
    .component('AppTableFieldsJS', AppTableFieldsJS)
    .component('AppTableJS', AppTableJS)
    .component('AppTablePage', AppTablePage)
    .component('AppTableSearch', AppTableSearch)
    .component('AppTableSearchJS', AppTableSearchJS)
    .component('AppTabs', AppTabs)
    .component('AppTreeForm', AppTreeForm)
    .component('AppTreeLabel', AppTreeLabel)
    .component('AppTreeNodes', AppTreeNodes)
    .component('AppTreePage', AppTreePage)
    .component('Fa', Fa)
    .use(pinia)
useUser().fetch().then(() => app.use(router).mount('#vue'))
