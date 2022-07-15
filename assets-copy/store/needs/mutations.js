export const mutations = {
    initiale(state) {
        state.needs = {...state.initiale.products}
        state.displayed = {}
    },
    initial(state) {
        state.needsComp = {...state.initiale.components}
        state.displayed = {}
    },
    needs(state, needs, needsComp) {
        state.needs = needs.products
        state.needsComp = needs.components
        state.initiale = {...needs}
    },
    show(state) {
        const needs = Object.entries(state.needs) 
        const components = Object.entries(state.needsComp)
        const leng = components.length

        const len = needs.length
        for (let i = 0; i < 5 && i < len; i++) {
            // eslint-disable-next-line @typescript-eslint/no-confusing-void-expression
            const [productId, need] = needs[i]
            state.displayed[productId] = need
          
            delete state.needs[productId]
        }
     
        state.page++
    },
    showCom(state) {
        const components = Object.entries(state.needsComp)
        console.log('showwwww', components);

        const leng = components.length
        for (let i = 0; i < 5 && i < leng; i++) {
            // eslint-disable-next-line @typescript-eslint/no-confusing-void-expression
            const [componentId, component] = components[i]
            state.displayed[componentId] = component

            delete state.needsComp[componentId]

        }
        state.page++
    },
    
    
}
