import {resolveComponent} from 'vue'

export default function AppNavbar() {
    const AppContainer = resolveComponent('AppContainer')
    return <nav className="bg-dark mb-1 navbar navbar-dark navbar-expand-sm">
        <AppContainer fluid>
            <span className="m-0 navbar-brand p-0">T-Concept</span>
        </AppContainer>
    </nav>
}
