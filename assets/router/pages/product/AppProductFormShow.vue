<template>
    <AppTabs id="gui-start" class="gui-start-content">
        <AppTab id="gui-start-main" active title="Généralités" icon="pencil" tabs="gui-start">
            <AppCardShow id="addGeneralites"/>
        </AppTab>
        <AppTab id="gui-start-quality" title="Qualité" icon="certificate" tabs="gui-start">
            <AppCardShow id="addQualite"/>
        </AppTab>
        <AppTab id="gui-start-purchase-logistics" title="Logistique" icon="pallet" tabs="gui-start">
            <AppCardShow id="addLogistique"/>
        </AppTab>
        <AppTab id="gui-start-Prix" title="Prix" icon="circle-dollar-to-slot" tabs="gui-start">
            <AppCardShow id="addPrice"/>
        </AppTab>
        <AppTab id="gui-start-addresses" title="Adresses\Contacts" icon="location-dot" tabs="gui-start">
            <AppCardShow id="addAdresses"/>
        </AppTab>
    </AppTabs>
</template>
