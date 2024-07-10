import {createRouter, createWebHistory} from 'vue-router'
import AppHome from '../components/pages/AppHome'
import AppLogin from '../components/pages/AppLogin.vue'
import hr from './hr'
import it from './it'
import logistics from './logistics'
import management from './management'
import production from './production'
import project from './project'
import purchase from './purchase'
import quality from './quality'
import selling from './selling'
import useUser from '../stores/security'

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
                        type: 'text',
                        colWidth: '800px'
                    },
                    {
                        label: 'Ind.',
                        name: 'indice',
                        type: 'text'
                    },
                    {
                        label: 'design',
                        name: 'designation',
                        type: 'text'
                    },
                    {
                        label: 'compagnie',
                        name: 'compagnie',
                        type: 'text'
                    },
                    {
                        label: 'client',
                        name: 'client',
                        type: 'text'
                    },
                    {
                        label: 'Stock',
                        name: 'stock',
                        type: 'text'
                    },
                    {
                        label: 'T-Chiffrage',
                        name: 'Temps Chiffrage',
                        type: 'text'
                    },
                    {
                        label: 'T-Atelier',
                        name: 'temps atelier',
                        type: 'text'
                    },
                    {
                        label: 'VP',
                        name: 'volu_previ',
                        type: 'text'
                    },
                    {
                        label: '3%VP',
                        name: '3pc_volu_previ',
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
                        label: 'Cmde',
                        name: 'cmde',
                        type: 'text'
                    },
                    {
                        label: 'Début Prod.',
                        name: 'debutProd',
                        type: 'date'
                    },
                    {
                        label: 'OF',
                        name: 'of',
                        type: 'number'
                    },
                    {
                        label: 'Indice OF',
                        name: 'Indice OF',
                        type: 'number'
                    },
                    {
                        label: 'Produit',
                        name: 'produit',
                        type: 'text'
                    },
                    {
                        label: 'Indice',
                        name: 'Indice',
                        type: 'number'
                    },
                    {
                        label: 'Quantité',
                        name: 'quantite',
                        type: 'text'
                    },
                    {
                        label: 'Site de production',
                        name: 'siteDeProduction',
                        type: 'text'
                    },
                    {
                        label: 'Confirmer OF',
                        name: 'confirmerOF',
                        type: 'boolean'
                    },

                ],
                fieldsCollapseOnGoingLocalOf: [
                    
                    {
                        label: 'Client',
                        name: 'client',
                        type: 'text'
                    },
                    {
                        label: 'Cmde',
                        name: 'cmde',
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
                        label: 'Produit',
                        name: 'produit',
                        type: 'text'
                    },
                    {
                        label: 'Indice',
                        name: 'Indice',
                        type: 'number'
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
                        name: 'Etat',
                        type: 'text'
                    },
                    {
                        label: 'Site de production',
                        name: 'siteDeProduction',
                        type: 'text'
                    },
                    {
                        label: 'OF',
                        name: 'of',
                        type: 'text'
                    },
                    {
                        label: 'Indice OF',
                        name: 'Indice OF',
                        type: 'number'
                    },

                ],
                fieldsCollapsenewOfs: [
                    {
                        label: 'Client',
                        name: 'client',
                        type: 'text'
                    },
                    {
                        label: 'Cmde',
                        name: 'cmde',
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
                        label: 'Produit',
                        name: 'produit',
                        type: 'text'
                    },
                    {
                        label: 'Quantité',
                        name: 'quantite',
                        type: 'text'
                    },
                    {
                        label: 'Site de production',
                        name: 'siteDeProduction',
                        type: 'text'
                    },
                    {
                        label: 'Etat initial OF',
                        name: 'etatInitialOF',
                        type: 'text'
                    },
                    {
                        label: 'Minimum de lancement',
                        name: 'minDeLancement',
                        type: 'number'
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
