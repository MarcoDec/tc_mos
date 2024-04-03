import {createRouter, createWebHistory} from 'vue-router'
//import AppAnalogClock from './pages/AppAnalogClock.vue'
import AppHome from '../components/pages/AppHome'
import AppLogin from '../components/pages/AppLogin.vue'
import hr from './hr'
import logistics from './logistics'
import management from './management'
import production from './production'
import project from './project'
import purchase from './purchase'
import quality from './quality'
import selling from './selling'
import useUser from '../stores/security'
import it from './it'

const optionsSiteDeProduction = [
    {text: '', value: null},
    {text: 'auto', value: 'auto'},
    {text: 'TUNISIE CONCEPT', value: 'TunisieConcept'}
]
const optionsEtatInitialOFduction = [
    {text: '', value: null},
    {text: 'confirmé', value: 'confirmé'},
    {text: 'brouillon', value: 'brouillon'}
]
const router = createRouter({
    history: createWebHistory(), routes: [
        {
            component: async () => import('./pages/AppProductionPlanning.vue'),
            meta: {requiresAuth: true},
            name: 'manufacturing-schedule',
            path: '/manufacturingSchedule',
            props: {
                fields: [

                    {
                        label: 'Produit',
                        name: 'produit',
                        type: 'text'
                    },
                    {
                        label: 'Ind.',
                        name: 'ind',
                        type: 'text'
                    },
                    {
                        label: 'Client',
                        name: 'client',
                        type: 'text'
                    },
                    {
                        label: 'Stocks',
                        name: 'stocks',
                        type: 'text'
                    },
                    {
                        label: '3%VP',
                        name: 'vp',
                        type: 'text'
                    },
                    {
                        label: 'Retard',
                        name: 'retard',
                        type: 'text'
                    }
                ],
                icon: 'table-list',
                title: 'Planning de production'
            }
        },
        {
            component: async () => import('./pages/AppManufacturingOrderNeeds.vue'),
            meta: {requiresAuth: true},
            name: 'manufacturing-order-needs',
            path: '/manufacturingOrderNeeds',
            props: {
                fieldsCollapseOfsToConfirm: [
                    {
                        label: 'Client',
                        name: 'client',
                        type: 'text'
                    },
                    {
                        label: 'Produit',
                        name: 'produit',
                        type: 'text'
                    },
                    {
                        label: 'Cmde',
                        name: 'cmde',
                        type: 'text'
                    },
                    {
                        label: 'OF',
                        name: 'of',
                        type: 'number'
                    },
                    {
                        label: 'Début Prod.',
                        name: 'debutProd',
                        type: 'date'
                    },
                    {
                        label: 'Fin Prod.',
                        name: 'finProd',
                        type: 'date'
                    },
                    {
                        label: 'Quantité',
                        name: 'quantite',
                        type: 'number'
                    },
                    {
                        label: 'Site de production',
                        name: 'siteDeProduction',
                        type: 'text'
                    },
                    {
                        label: 'Quantité produite',
                        name: 'quantiteProduite',
                        type: 'number'
                    },
                    {
                        label: 'Confirmer OF',
                        name: 'confirmerOF',
                        type: 'boolean'
                    }
                ],
                fieldsCollapseOnGoingLocalOf: [

                    {
                        label: 'Client',
                        name: 'client',
                        type: 'text'
                    },
                    {
                        label: 'Produit',
                        name: 'produit',
                        type: 'text'
                    },
                    {
                        label: 'Cmde',
                        name: 'cmde',
                        type: 'text'
                    },
                    {
                        label: 'OF',
                        name: 'of',
                        type: 'number'
                    },
                    {
                        label: 'Début Prod.',
                        name: 'debutProd',
                        type: 'date'
                    },
                    {
                        label: 'Fin Prod.',
                        name: 'finProd',
                        type: 'date'
                    },
                    {
                        label: 'Quantité',
                        name: 'quantite',
                        type: 'number'
                    },
                    {
                        label: 'Quantité Produite',
                        name: 'quantiteProduite',
                        type: 'number'
                    },
                    {
                        label: 'Etat',
                        name: 'etat',
                        type: 'text'
                    }
                ],
                fieldsCollapsenewOfs: [

                    {
                        label: 'Client',
                        name: 'client',
                        type: 'text'
                    },
                    {
                        label: 'Produit',
                        name: 'produit',
                        type: 'text'
                    },
                    {
                        label: 'Cmde',
                        name: 'cmde',
                        type: 'text'
                    },
                    {
                        label: 'Minimum de lancement',
                        name: 'minDeLancement',
                        type: 'number'
                    },
                    {
                        label: 'Qté demandée',
                        name: 'qteDemandee',
                        type: 'number'
                    },
                    {
                        label: 'OFs associés',
                        name: 'ofsAssocies',
                        type: 'text'
                    },
                    {
                        label: 'Début Prod.',
                        name: 'debutProd',
                        type: 'date'
                    },
                    {
                        label: 'Fin Prod.',
                        name: 'finProd',
                        type: 'date'
                    },
                    {
                        label: 'Quantité',
                        name: 'quantite',
                        type: 'text'
                    },
                    {
                        label: 'Site de production',
                        name: 'siteDeProduction',
                        options: {label: value => optionsSiteDeProduction.find(option => option.type === value)?.text ?? null, options: optionsSiteDeProduction},
                        type: 'select'
                    },
                    {
                        label: 'Etat initial OF',
                        name: 'etatInitialOF',
                        options: {label: value => optionsEtatInitialOFduction.find(option => option.type === value)?.text ?? null, options: optionsEtatInitialOFduction},
                        type: 'select'
                    },
                    {
                        label: 'Lancer OF',
                        name: 'lancerOF',
                        type: 'boolean'
                    }
                ],
                icon: 'table-list',
                title: 'Calcul des besoins'
            }
        },
        ...hr,
        ...it,
        ...logistics,
        ...management,
        ...production,
        ...project,
        ...purchase,
        ...quality,
        ...selling,
        {component: AppLogin, meta: {title: 'Connexion — T-Concept GPAO'}, name: 'login', path: '/login'},
        {component: AppHome, meta: {title: 'T-Concept GPAO'}, name: 'home', path: '/'},
        {meta: {title: 'T-Concept GPAO'}, name: 'all', path: '/:pathMatch(.*)*'}
    ]
})

// eslint-disable-next-line consistent-return
router.beforeEach(to => {
    const user = useUser()
    if (
        to.matched.some(record => record.name === 'login') && user.isLogged
        || to.matched.some(record => record.name === 'all')
    )
        return {name: 'home'}
    if (to.matched.some(record => record.name !== 'login') && !user.isLogged)
        return {name: 'login'}
})

export default router
