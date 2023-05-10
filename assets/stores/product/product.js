import api from '../../api'
import {defineStore} from 'pinia'

export default function generateProduct(products) {
    console.log('pppp', products);
    const id = Number(products['@id'].match(/\d+/));

    return defineStore(`${products['@id']}`, {
        actions: {
            dispose() {
                this.$reset()
                this.$dispose()
            },
            
            async updateProduction(data) {
                const response = await api(`/api/products/${id}/production`, 'PATCH', data)
                this.$state = {...response}
                console.log('updateQuality', response)
            }, 
            
            async updateAdmin(data) {
                const response = await api(`/api/products/${id}/admin`, 'PATCH', data)
                this.$state = {...response}
                console.log('update=', response)
            },
            async updateMain(data) {
                const response = await api(`/api/products/${id}/main`, 'PATCH', data)
                this.$state = {...response}
                console.log('update=', response)
            },
            async updateProject(data) {
                const response = await api(`/api/products/${id}/project`, 'PATCH', data)
                this.$state = {...response}
                console.log('updateProject', response)
            },
            async updateLogistique(data) {
                const response = await api(`/api/products/${id}/logistics`, 'PATCH', data)
                this.$state = {...response}
                console.log('updatepurchase-logistics', response)
            },

        },
        getters: {
            date: (state) => new Date(state.endOfLife),
            getDate: (state) =>
              state.date.getDate() < 9
                ? `0${state.date.getDate()}`
                : state.date.getDate(),
            getMonth: (state) =>
              state.date.getMonth() < 9
                ? `0${state.date.getMonth() + 1}`
                : state.date.getMonth() + 1,
            getEndOfLife: (state) =>
              `${state.date.getFullYear()}-${state.getMonth}-${state.getDate}`,

            // getAddress: state => state.address.address,
            // getAddress2: state => state.address.address2,
            // getCity: state => state.address.city,
            // getCountry: state => state.address.country,
            // getEmail: state => state.address.email,
            // getPhone: state => state.address.phoneNumber,
            // getPostal: state => state.address.zipCode,
            // incotermsValue: state => (state.incoterms ? state.incoterms['@id'] : null),
            // vatMessageValue: state => (state.vatMessage ? state.vatMessage['@id'] : null)

        },
        state: () => ({...products})
    })()
}
