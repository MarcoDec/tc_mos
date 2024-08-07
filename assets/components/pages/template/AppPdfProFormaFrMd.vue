<script setup>
    import {usePDF} from 'vue3-pdfmake'

    const pdfmake = usePDF({
        autoInstallVFS: true
    })

    const onGenPDF = async () => {
        pdfmake.createPdf({
            content: [
                {
                    columns: [
                        {

                            text: 'ProForma FR-MD N° 5404',
                            style: 'header',
                            alignment: 'left'

                        }
                    ]
                },
                {
                    canvas: [
                        {
                            type: 'line',
                            x1: 0,
                            y1: 10,
                            x2: 595 - 2 * 40,
                            y2: 10,
                            lineWidth: 1,
                            lineColor: '#D3D3D3'
                        }
                    ],
                    margin: [0, 0, 0, 10]
                },
                {
                    columns: [
                        {
                            width: '*',
                            text: [
                                {text: 'Expéditeur :\n', style: 'contactInfo', color: '#6c757d'},
                                {text: 'STE PROD-ELEC TUNISIE\n', style: 'subheader'},
                                {text: 'Rue de 1er Juin Kalaâ-Kebira\n', style: 'headerInfo'},
                                {text: '4060 SOUSSE\n', style: 'headerInfo'},
                                {text: 'Tunisia\n', style: 'headerInfo'},
                                {text: 'Tel. 73356150\n', style: 'contactInfo'},
                                {text: 'Fax.\n', style: 'contactInfo'},
                                {text: [{text: 'Email: ', style: 'contactInfo'}, {text: 'secretariat.tn@tconcept.fr', color: '#0d6efd', fontSize: 8}]}
                            ]
                        },
                        {
                            stack: [
                                {text: 'Destinataire :\n', style: 'contactInfo', alignment: 'right', color: '#6c757d'}
                            ],
                            width: '50%'
                        },
                        {
                            table: {
                                widths: ['*'],
                                body: [
                                    [
                                        {
                                            text: [
                                                {text: 'TCONCEPT\n', style: 'subheader'},
                                                {text: '5 rue Alfred Nobel\n', style: 'headerInfo'},
                                                {text: 'ZA La charrière\n', style: 'headerInfo'},
                                                {text: '70190 RIOZ\n', style: 'headerInfo'},
                                                {text: 'France\n', style: 'headerInfo'},
                                                {text: 'Tel. +33 (0)3 84 91 98 84\n', style: 'contactInfo'},
                                                {text: 'Fax. +33 (0)3 84 91 98 70\n', style: 'contactInfo'},
                                                {text: [{text: 'Email: ', style: 'contactInfo'}, {text: 'secretariat@tconcept.fr', color: '#0d6efd', fontSize: 8}]}
                                            ]

                                        }
                                    ]
                                ]
                            },
                            layout: {
                                fillColor: '#e9ecef',
                                hLineWidth() {
                                    return 0.1 // épaisseur de ligne horizontale très fine
                                },
                                vLineWidth() {
                                    return 0.1 // épaisseur de ligne verticale très fine
                                },
                                hLineColor: '#D3D3D3',
                                vLineColor: '#D3D3D3'
                            },
                            margin: [-50, 20, 0, 0]
                        }
                    ],
                    margin: [0, 0, 0, 10]
                },
                {
                    table: {
                        headerRows: 1,
                        widths: [170, '*', '*', 170, '*'],
                        body: [
                            [
                                {text: 'ProForma N°', style: 'tableHeader'},
                                {text: 'Date', style: 'tableHeader'},
                                {text: 'Incoterm', style: 'tableHeader'},
                                {text: 'Destination', style: 'tableHeader'},
                                {text: 'ML', style: 'tableHeader'}
                            ],
                            [
                                {text: '5404', style: 'tableBody'},
                                {text: '02/02/24', style: 'tableBody'},
                                {text: 'DAP', style: 'tableBody'},
                                {text: '10 - CAMION TN-FR', style: 'tableBody'},
                                {text: '', style: 'tableBody'}
                            ]
                        ]
                    },
                    layout: {
                        fillColor(rowIndex) {
                            return rowIndex === 0 ? '#e9ecef' : null
                        },
                        hLineWidth() {
                            return 0.01 // épaisseur de ligne horizontale très fine
                        },
                        vLineWidth() {
                            return 0.01 // épaisseur de ligne verticale très fine
                        },
                        hLineColor: '#D3D3D3',
                        vLineColor: '#D3D3D3'
                    },
                    margin: [0, 10, 0, 10]
                },
                {
                    table: {
                        widths: ['*', '*', '*', '*', '*', '*'],
                        body: [
                            [
                                {text: 'Code Douane', style: 'tableHeader'},
                                {text: 'Libelle', style: 'tableHeader'},
                                {text: 'Origine', style: 'tableHeader'},
                                {text: 'Qte', style: 'tableHeader'},
                                {text: 'Poids (Kg)', style: 'tableHeader'},
                                {text: 'Prix total', style: 'tableHeader'}
                            ],
                            [
                                {text: '', style: 'tableBody'},
                                {colSpan: '3', text: 'Total', margin: [0, 5, 0, 5]},
                                {text: '', style: 'tableBody'},
                                {text: '', style: 'tableBody'},
                                {text: '', style: 'tableBody'},
                                {text: '0,00€', style: 'tableBody'}
                            ]
                        ]
                    },
                    layout: {
                        fillColor(rowIndex) {
                            return rowIndex === 0 ? '#e9ecef' : null
                        },
                        hLineWidth() {
                            return 0.01
                        },
                        vLineWidth() {
                            return 0.01
                        },
                        hLineColor: '#D3D3D3',
                        vLineColor: '#D3D3D3'
                    },
                    margin: [0, 10, 0, 10]
                },
                {text: 'L\'exportateur des produits couverts par le présent document (autorisation douanière n* FR000460/0141) déclare que sauf indication claire du contraire ces produits sont d\'origine préférencielle CE.\n', margin: [0, 0, 0, 0]},
                {text: 'VALEUR UNIQUEMENT POUR LA DOUANE.\n'},
                {text: 'EXPORTATION TEMPORAIRE POUR TOUS LES PRODUITS LISTES CI-DESSOUS.\n'}
            ],

            styles: {
                header: {
                    fontSize: 18,
                    bold: true
                },
                subheader: {
                    fontSize: 10,
                    bold: true
                },
                headerInfo: {
                    bold: true,
                    fontSize: 8
                },
                contactInfo: {
                    fontSize: 8
                },
                tableHeader: {
                    fontSize: 8,
                    bold: true,
                    color: 'black'
                },
                tableBody: {
                    fontSize: 8
                },
                footer: {
                    italics: true,
                    fontSize: 8,
                    color: 'gray'
                }
            },
            defaultStyle: {
                fontSize: 8
            }
        }).open()
    }
</script>

<template>
    <button @click="onGenPDF">
        Click here to download demo PDF
    </button>
</template>
