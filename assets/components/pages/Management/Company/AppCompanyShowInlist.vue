<script setup>
    import AppCompanyListEmploye from './bottom/AppCompanyListEmploye.vue'
    import AppCompanyListZone from './bottom/AppCompanyListZone.vue'
    import AppCompanyListWarehouse from './bottom/AppCompanyListWarehouse.vue'
    import AppCompanyListSupply from './bottom/AppCompanyListSupply.vue'
    import AppCompanyListSupplier from './bottom/AppCompanyListSupplier.vue'
    import AppCompanyListEvent from './bottom/AppCompanyListEvent.vue'
    import AppEquipementListUserAbility from '../../equipement/bottom/AppEquipementListUserAbility.vue'
    import AppEquipementListEvent from '../../equipement/bottom/AppEquipementListEvent.vue'
    import AppEquipementListPrevision from '../../equipement/bottom/AppEquipementListPrevision.vue'
</script>

<template>
    <div>
        <AppTabs id="gui-start-bottom" class="gui-start-content-bottom" format-nav="block">
            <AppTab id="gui-start-company-employe" active title="Employés" icon="user-circle" tabs="gui-start-bottom">
                <AppSuspense>
                    <AppCompanyListEmploye/>
                </AppSuspense>
            </AppTab>
            <AppTab id="gui-start-company-zone" title="Zones" icon="location-dot" tabs="gui-start-bottom">
                <AppSuspense>
                    <AppCompanyListZone/>
                </AppSuspense>
            </AppTab>
            <AppTab id="gui-start-company-entrepot" title="Entrepôt" icon="warehouse" tabs="gui-start-bottom">
                <AppSuspense>
                    <AppCompanyListWarehouse/>
                </AppSuspense>
            </AppTab>
            <AppTab id="gui-start-company-fourniture" title="Fournitures" icon="layer-group" tabs="gui-start-bottom">
                <AppSuspense>
                    <AppCompanyListSupply/>
                </AppSuspense>
            </AppTab>
            <AppTab id="gui-start-company-fournisseur" title="Fournisseur" icon="truck-field" tabs="gui-start-bottom">
                <AppSuspense>
                    <AppCompanyListSupplier/>
                </AppSuspense>
            </AppTab>
            <AppTab id="gui-start-company-event" title="Evènements" icon="calendar-day" tabs="gui-start-bottom">
                <AppSuspense>
                    <AppCompanyListEvent/>
                </AppSuspense>
            </AppTab>
            <AppTab id="gui-start-equipement-user" title="Employés habilités" icon="users" tabs="gui-start-bottom">
                <AppSuspense>
                    <AppEquipementListUserAbility/>
                </AppSuspense>
            </AppTab>
            <AppTab id="gui-start-equipement-event" title="Evènements" icon="calendar-day" tabs="gui-start-bottom">
                <AppSuspense>
                    <AppEquipementListEvent/>
                </AppSuspense>
            </AppTab>
            <AppTab id="gui-start-equipement-prevision" title="Entretiens prévisionnels" icon="wrench" tabs="gui-start-bottom">
                <AppSuspense>
                    <AppEquipementListPrevision/>
                </AppSuspense>
            </AppTab>
        </AppTabs>
    </div>
</template>

<style scoped>
    div.active { position: relative; z-index: 0; overflow: scroll; max-height: 100%}
</style>
