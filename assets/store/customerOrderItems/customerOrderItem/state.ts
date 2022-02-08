export type State = {
    produit: string | null
    ref: string | null
    quantitéSouhaitée: number | null
    dateLivraisonSouhaitée: string | null
    quantitéConfirmée: number | null
    dateLivraisonConfirmée: string | null
    etat: string | null
    description: string | null
    delete: boolean | null
    update: boolean | null
    id: number
}
