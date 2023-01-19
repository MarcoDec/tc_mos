import {defineStore} from 'pinia'

export default function generateAttribute(attribute) {
    return defineStore(`attributes/${attribute.id}`, {
        actions: {
            dispose() {
                this.$reset()
                this.$dispose()
            },
            update(attributes, family) {
                if (this.includes(family) && !attributes.includes(this['@id']))
                    this.families = this.families.filter(item => item !== family['@id'])
                else if (!this.includes(family) && attributes.includes(this['@id']))
                    this.families.push(family['@id'])
            }
        },
        getters: {
            includes: state => family => state.families.includes(family['@id'])
        },
        state: () => ({...attribute})
    })()
}
