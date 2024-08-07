<script setup>
    import {usePDF} from 'vue3-pdfmake'
    import logo from './img/TConcept_Logo.png'

    // Convertir le logo en base64
    const toBase64 = url => new Promise((resolve, reject) => {
        const xhr = new XMLHttpRequest()
        xhr.onload = () => {
            const reader = new FileReader()
            reader.onloadend = () => {
                resolve(reader.result)
            }
            reader.readAsDataURL(xhr.response)
        }
        xhr.onerror = () => {
            reject(xhr.statusText)
        }
        xhr.open('GET', url)
        xhr.responseType = 'blob'
        xhr.send()
    })

    const pdfmake = usePDF({
        autoInstallVFS: true
    })

    const onGenPDF = async () => {
        const logoBase64 = await toBase64(logo)

        pdfmake.createPdf({
            content: [
                {
                    canvas: [
                        {
                            type: 'line',
                            x1: 0,
                            y1: 10,
                            x2: 595 - 2 * 40, // largeur de la page - marges
                            y2: 10,
                            lineWidth: 1,
                            lineColor: '#D3D3D3' // Gris clair
                        }
                    ],
                    margin: [0, 0, 0, 10] // marges pour l'espacement
                },
                {
                    columns: [
                        {
                            image: logoBase64,
                            width: 150,
                            margin: [0, 10, 0, 10]
                        },
                        {
                            table: {
                                widths: ['*'],
                                body: [
                                    [
                                        {
                                            text: 'BON DE LIVRAISON\nN° 50752\n',
                                            alignment: 'center',
                                            style: 'header'
                                        }
                                    ]
                                ]
                            },
                            layout: {
                                fillColor: '#3788d8'
                            },
                            margin: [100, 20, 0, 0]
                        }
                    ]
                },
                {
                    columns: [
                        {
                            width: '*',
                            text: [
                                {text: 'TCONCEPT\n', style: 'subheader'},
                                {text: '5 rue Alfred Nobel\n', style: 'headerInfo'},
                                {text: 'ZA La charrière\n', style: 'headerInfo'},
                                {text: '70190 RIOZ\n', style: 'headerInfo'},
                                {text: 'France\n', style: 'headerInfo'},
                                {text: 'Tel. +33 (0)3 84 91 98 84\n', style: 'contactInfo'},
                                {text: 'Fax. +33 (0)3 84 91 98 70\n', style: 'contactInfo'},
                                {text: [{text: 'Email: ', style: 'contactInfo'}, {text: 'tconcept@orange.fr', color: '#0d6efd', fontSize: 8}]}
                            ]
                        },
                        {
                            table: {
                                widths: ['*'],
                                body: [
                                    [
                                        {
                                            text: [
                                                {text: 'STELLANTIS ZARAGOZA\n', style: 'subheader'},
                                                {text: 'Pol Ind Entrerios\n', style: 'headerInfo'},
                                                {text: 'Carretera Nacional 232 km 29\n', style: 'headerInfo'},
                                                {text: '50639 Figueruelas Zaragoza\n', style: 'headerInfo'},
                                                {text: 'Spain\n', style: 'headerInfo'}
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
                                }
                            },
                            margin: [0, 20, 0, 0]
                        }
                    ]
                },
                {
                    canvas: [
                        {
                            type: 'line',
                            x1: 0,
                            y1: 0,
                            x2: 595 - 2 * 220, // largeur de la page - marges
                            y2: 0,
                            lineWidth: 1,
                            lineColor: '#D3D3D3' // Gris clair
                        }
                    ],
                    margin: [0, 10, 0, 10] // marges pour l'espacement
                },
                {
                    table: {
                        headerRows: 1,
                        widths: ['*', '*', '*', '*'],
                        body: [
                            [
                                {text: 'Date', style: 'tableHeader'},
                                {text: 'Votre référence', style: 'tableHeader'},
                                {text: 'Incoterm', style: 'tableHeader'},
                                {}
                            ],
                            [
                                {text: '01/02/2024', style: 'tableBody'},
                                {text: '02022024Z', style: 'tableBody'},
                                {text: 'FCA', style: 'tableBody'},
                                {text: '', style: 'tableBody'}
                            ]
                        ]
                    },
                    layout: {
                        fillColor(rowIndex) {
                            return rowIndex % 2 === 0 ? '#e9ecef' : null
                        },
                        hLineWidth() {
                            return 0.01 // épaisseur de ligne horizontale très fine
                        },
                        vLineWidth() {
                            return 0.01 // épaisseur de ligne verticale très fine
                        }
                    },
                    margin: [0, 10, 0, 10]
                },
                {
                    table: {
                        widths: [50, '*', 50, 50, 50],
                        body: [
                            [
                                {text: 'Référence', style: 'tableHeader'},
                                {text: 'Désignation', style: 'tableHeader'},
                                {text: 'Code douanier', style: 'tableHeader'},
                                {text: 'N° de lot', style: 'tableHeader'},
                                {text: 'Quantité', style: 'tableHeader'}
                            ],
                            [
                                {text: '9854833280', style: 'tableBody'},
                                {text: 'PO 87265590 - FCA P2JO-MCM VOL FM2 - Indice: 00C', style: 'tableBody'},
                                {text: '8544300089', style: 'tableBody'},
                                {text: '363.1', style: 'tableBody'},
                                {text: '200', style: 'tableBody'}
                            ],
                            [
                                {text: '9856554780', style: 'tableBody'},
                                {text: 'PO 87265513 - FCA P21 MV VOL AMFM1 - Indice: 00', style: 'tableBody'},
                                {text: '8544301089', style: 'tableBody'},
                                {text: '354.1', style: 'tableBody'},
                                {text: '200', style: 'tableBody'}
                            ],
                            [
                                {text: '', margin: [0, 180, 0, 180]}, {}, {}, {}, {}
                            ]
                        ]
                    },
                    layout: {
                        fillColor(rowIndex) {
                            return rowIndex % 2 === 0 ? '#e9ecef' : null
                        },
                        hLineWidth() {
                            return 0.01 // épaisseur de ligne horizontale très fine
                        },
                        vLineWidth() {
                            return 0.01 // épaisseur de ligne verticale très fine
                        }
                    },
                    margin: [0, 10, 0, 10]
                },
                {
                    table: {
                        widths: ['*', '*', '*', '*', '*'],
                        body: [
                            [
                                '',
                                {text: 'Poids net total: 62.4 Kg', style: 'totalTableBody'},
                                '',
                                '',
                                ''
                            ]
                        ]
                    }
                }
            ],
            footer() {
                return {
                    stack: [
                        {
                            text: 'RCS Vesoul - SIREN 47913401700025 - APE 7112B - TVA Intracommunautaire FR94479134017 - SARL- Capital 1000000€',
                            style: 'footer',
                            alignment: 'center'
                        },
                        {
                            text: 'CERTIFICAT DE CONFORMITÉ',
                            style: 'footerPlus',
                            alignment: 'center'
                        },
                        {
                            text: 'Nous certifions que les produits mentionnés sur ce bordereau de livraison sont conformes à leurs spécifications et aux commandes contractuelles.',
                            style: 'footerPlus',
                            alignment: 'center'
                        },
                        {
                            text: 'Ce document est édité informatiquement et n\'est donc pas signé.',
                            style: 'footerPlus',
                            alignment: 'center'
                        }

                    ]
                }
            },
            styles: {
                header: {
                    fontSize: 18,
                    bold: true
                },
                subheader: {
                    fontSize: 14,
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
                totalTableBody: {
                    fontSize: 9,
                    alignment: 'right',
                    bold: true
                },
                footer: {
                    italics: true,
                    fontSize: 7
                },
                footerPlus: {
                    bold: true,
                    fontSize: 7
                }
            },
            defaultStyle: {
                fontSize: 10
            }
        }).open()
    }
</script>

<template>
    <button @click="onGenPDF">
        Click here to download demo PDF
    </button>
</template>
