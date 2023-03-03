import {Suspense, defineAsyncComponent, h} from 'vue'

function AppSuspenseWrapper(props) {
    return h(Suspense, h(defineAsyncComponent(props.component), props.properties))
}

AppSuspenseWrapper.displayName = 'AppSuspenseWrapper'
AppSuspenseWrapper.props = {
    component: {required: true, type: Function},
    properties: {default: () => ({}), type: Object}
}

export default AppSuspenseWrapper
