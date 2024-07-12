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

                            text: 'ProForma TN-FR N° 5404',
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
                                {text: 'Contact', style: 'tableHeader'},
                                {text: 'Incoterm', style: 'tableHeader'},
                                {text: 'Destination', style: 'tableHeader'}
                            ],
                            [
                                {text: '5404', style: 'tableBody'},
                                {text: '02/02/24', style: 'tableBody'},
                                {text: 'BOUSLEMA', style: 'tableBody'},
                                {text: 'DAP', style: 'tableBody'},
                                {text: '10 - CAMION TN-FR', style: 'tableBody'}
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
                {text: '\n'},
                {
                    text: 'Toteaux',
                    style: 'subheader'
                },
                {
                    table: {
                        widths: ['auto', '*', '*', '*', '*'],
                        body: [
                            [
                                {text: 'Code Douane', style: 'tableHeader'},
                                {text: 'Type de marchandise', style: 'tableHeader'},
                                {text: 'Poids brut', style: 'tableHeader'},
                                {text: 'Poids net', style: 'tableHeader'},
                                {text: 'Valeur marchandise', style: 'tableHeader'}
                            ],
                            [
                                {text: '8544300089', style: 'tableBody'},
                                {text: 'Faisceaux électriques', style: 'tableBody'},
                                {text: '444.6 Kg', style: 'tableBody'},
                                {text: '342 Kg', style: 'tableBody'},
                                {text: '11202,24€', style: 'tableBody'}
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
                {text: '\n'},
                {
                    text: 'Détail',
                    style: 'subheader'
                },
                {
                    table: {
                        headerRows: 1,
                        widths: [10, '*', '*', '*', '*'],
                        body: [
                            [
                                {text: '#', style: 'tableHeader'},
                                {text: 'Reference', style: 'tableHeader'},
                                {text: 'Désignation', style: 'tableHeader'},
                                {text: 'N° Lot', style: 'tableHeader'},
                                {text: 'Quantité', style: 'tableHeader'}
                            ],
                            [
                                {text: '1', style: 'tableBody'},
                                {text: '9813715280', style: 'tableBody', color: '#0d6efd'},
                                {text: 'FCA B818 HAB AM FM DAB', style: 'tableBody'},
                                {text: '', style: 'tableBody'},
                                {text: '1200', style: 'tableBody', color: '#0d6efd'}
                            ],
                            [
                                {text: '2', style: 'tableBody'},
                                {text: '9813715480', style: 'tableBody', color: '#0d6efd'},
                                {text: 'FCA B818 HAB AM FM DAB GPS', style: 'tableBody'},
                                {text: '', style: 'tableBody'},
                                {text: '400', style: 'tableBody', color: '#0d6efd'}
                            ],
                            [
                                {colSpan: '5', text: '', margin: [0, 5, 0, 5]},
                                '',
                                '',
                                '',
                                ''
                            ],
                            [
                                '',
                                {text: 'Total', style: 'tableBody'},
                                '',
                                '',
                                {text: '1600', style: 'tableBody'}
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
                {text: 'L\'exportateur des produits couverts par le présent document (autorisation douanière n* FR000460/0141) déclare que sauf indication claire du contraire ces produits sont d\'origine préférencielle UE.\n', margin: [0, 0, 0, 0]},
                {text: 'VALEUR UNIQUEMENT POUR LA DOUANE.\n'}
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
