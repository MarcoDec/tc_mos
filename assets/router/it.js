import AppTablePageSuspense from '../components/pages/table/AppTablePageSuspense.vue'
import {readonly} from 'vue'
import AppEngineList from "../components/pages/production/equipment/list/AppEngineList.vue";

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
    }
]
