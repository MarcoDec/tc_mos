<template>
    <AppTabs id="gui-start" class="gui-start-content">
        <AppTab id="gui-start-main" active title="Généralités" icon="pencil">
            <AppCardShow id="addGeneralites"/>
        </AppTab>
        <AppTab id="gui-start-quality" title="Qualité" icon="certificate">
            <AppCardShow id="addQualite"/>
        </AppTab>
        <AppTab id="gui-start-purchase-logistics" title="Logistique" icon="pallet">
            <AppCardShow id="addLogistique"/>
        </AppTab>
        <AppTab id="gui-start-Prix" title="Prix" icon="circle-dollar-to-slot">
            <AppCardShow id="addPrice"/>
        </AppTab>
        <AppTab id="gui-start-addresses" title="Adresses\Contacts" icon="location-dot">
            <AppCardShow id="addAdresses"/>
        </AppTab>
    </AppTabs>
</template>
