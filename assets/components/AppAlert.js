import {make} from './ComponentBuilder'

export default make({
    exposedProps: {variant: 'danger'},
    name: 'AppAlert',
    props() {
        return {class: `alert alert-${this.variant}`, role: 'alert'}
    },
    slots: ['default'],
    tag: 'div'
})
