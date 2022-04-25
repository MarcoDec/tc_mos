import {Country} from '../modules'
import {Repository} from '@vuex-orm/core'
import fetchApi from '../../api'

export default class CountryRepository extends Repository {
    use = Country

    get options() {
        let options = {}
        for (const country of this.all()) {
            let option = options[country.group]
            if (typeof option === 'undefined') {
                option = {options: [], text: country.group}
                options[country.group] = option
            }
            option.options.push(country.option)
        }
        options = Object.values(options)
        for (const option of options)
            option.options.sort((a, b) => a.text.localeCompare(b.text))
        return options.sort((a, b) => b.text.localeCompare(a.text))
    }

    get optionsId() {
        return this.options
    }

    async load() {
        const response = await fetchApi('/api/countries', 'GET', null)
        this.fresh(response['hydra:member'])
    }
}
