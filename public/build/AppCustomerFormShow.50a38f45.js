import{f as o,b as u,D as c,C as s,g as a}from"./vendor.3c5bca8b.js";const y={__name:"AppCustomerFormShow",setup(b){const i=[{text:"aaaaa",value:"aaaaa"},{text:"bbbb",value:"bbbb"}],d=[{label:"Qualit\xE9",name:"Qualit\xE9",type:"number"},{label:"Nb PPM",name:"NbPPM",type:"number"},{label:"url *",name:"url",type:"text"},{label:"ident",name:"ident",type:"text"},{label:"password",name:"password",type:"password"}],r=[{label:"Nombre de bordereau ",name:"NombreBordereau",type:"number"},{label:"Dur\xE9eTransport",name:"Dur\xE9eTransport",type:"number"},{label:"Encours maximum",name:"EncourMaximum",type:"number"},{label:"url *",name:"url",type:"text"},{label:"ident",name:"ident",type:"text"},{label:"password",name:"password",type:"password"},{label:"incoterm",name:"incoterm",options:{label:n=>{var l,e;return(e=(l=i.find(t=>t.type===n))==null?void 0:l.text)!=null?e:null},options:i},type:"select"},{label:"commande minimum",name:"commandeMinimum",type:"number"}],p=[{label:"compte de comptabilit\xE9",name:"compteComptabilit\xE9",type:"text"},{label:"forcer la TVA",name:"forcerTVA",options:{label:n=>{var l,e;return(e=(l=i.find(t=>t.type===n))==null?void 0:l.text)!=null?e:null},options:i},type:"select"},{label:"montant de la facture minimum",name:"montantFactureMinimum",type:"text"},{label:"message TVA",name:"messageTVA",options:{label:n=>{var l,e;return(e=(l=i.find(t=>t.type===n))==null?void 0:l.text)!=null?e:null},options:i},type:"select"},{label:"conditions de paiement",name:"conditionsPaiement",options:{label:n=>{var l,e;return(e=(l=i.find(t=>t.type===n))==null?void 0:l.text)!=null?e:null},options:i},type:"select"},{label:"nb de factures",name:"nbFactures",type:"number"},{label:"url *",name:"url",type:"text"},{label:"ident",name:"ident",type:"text"},{label:"password",name:"password",type:"password"},{label:"Envoi facture par email",name:"EnvoiFactureEmail",type:"boolean"}];return(n,l)=>{const e=o("AppCardShow"),t=o("AppTab"),m=o("AppTabs");return u(),c(m,{id:"gui-start",class:"gui-start-content"},{default:s(()=>[a(t,{id:"gui-start-main",active:"",title:"G\xE9n\xE9ralit\xE9s",icon:"pencil"},{default:s(()=>[a(e,{id:"addGeneralites"})]),_:1}),a(t,{id:"gui-start-files",title:"Fichiers",icon:"laptop"},{default:s(()=>[a(e,{id:"addFichiers"})]),_:1}),a(t,{id:"gui-start-quality",title:"Qualit\xE9",icon:"certificate"},{default:s(()=>[a(e,{id:"addQualite",fields:d},null,8,["fields"])]),_:1}),a(t,{id:"gui-start-purchase-logistics",title:"Logistique",icon:"pallet"},{default:s(()=>[a(e,{id:"addLogistique",fields:r})]),_:1}),a(t,{id:"gui-start-accounting",title:"Comptabilit\xE9",icon:"industry"},{default:s(()=>[a(e,{id:"addComptabilite",fields:p},null,8,["fields"])]),_:1}),a(t,{id:"gui-start-addresses",title:"Adresses",icon:"location-dot"},{default:s(()=>[a(e,{id:"addAdresses"})]),_:1}),a(t,{id:"gui-start-contacts",title:"Contacts",icon:"file-contract"},{default:s(()=>[a(e,{id:"addContacts"})]),_:1})]),_:1})}}};export{y as default};
