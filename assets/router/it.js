import AppTablePageSuspense from '../components/pages/table/AppTablePageSuspense.vue'
import {readonly} from 'vue'
import AppEngineList from "../components/pages/production/equipment/list/AppEngineList.vue";
import AppShowGuiTestCounterPart
    from "../components/pages/production/equipment/show/test-counter-part/AppShowGuiTestCounterPart.vue";
import AppShowGuiInformatique from "../components/pages/it/equipment/show/informatique/AppShowGuiInformatique.vue";

export default [
    {
        component: AppEngineList,
        meta: {title: 'Informatique — T-Concept GPAO'},
        name: 'informatiques',
        path: '/informatiques',
        props: {
            icon: 'laptop-code',
            title: 'Eléments Informatique',
            engineType: 'informatique'
        }
    },
    {
        component: AppShowGuiInformatique,
        meta: {container: false, title: 'Test Counter Part — T-Concept GPAO'},
        name: 'informatiqueShow',
        path: '/informatique/:id_engine'
    },
]
