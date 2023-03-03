import type {State} from '.'


export type Notif = {category:string|null, subject: string|null, creationDatetime:string|null}

export type Getters = {
    color: (state: State) => string
    count: (state: State) => number|null
    getListNotif: (state: State) => Notif

}
export type GettersValues = {
    readonly [key in keyof Getters]: ReturnType<Getters[key]>
}
export const getters: Getters = {
    color: state => (state.read ? '#adb5bd5e' : 'none'),
    count: state => Object.values(state).filter(({read}) => read).length,
    getListNotif: state =>  ({category: state.category, subject:state.subject, creationDatetime:state.creationDatetime}),


}


