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
                                            text: 'ProForma\n N° 50701\n',
                                            alignment: 'center',
                                            style: 'header'
                                        }
                                    ]
                                ]
                            },
                            layout: {
                                fillColor: '#A9E2F3',
                                hLineColor: '#D3D3D3',
                                vLineColor: '#D3D3D3'
                            },
                            margin: [100, 20, 0, 0]
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
                                {text: 'Émetteur :\n', style: 'contactInfo'},
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
                            stack: [
                                {text: 'Adressé à :\n', style: 'contactInfo'},
                                {text: 'PCA SLOVAKIA SRO\n', style: 'subheader'},
                                {text: 'Automobilova ulica 1, 91701 Trnava\n', style: 'headerInfo'},
                                {text: 'C/O PSA COMPTABILITE FOURNISSEURS TSA 17833\n', style: 'headerInfo'},
                                {text: '62971 ARRAS\n', style: 'headerInfo'},
                                {text: 'France\n', style: 'headerInfo'},
                                {text: 'Tel. +421915759107\n', style: 'contactInfo'},
                                {text: 'SK2021746817\n', style: 'contactInfo'}
                            ],
                            width: '50%'
                        }
                    ],
                    margin: [0, 0, 0, 10]
                },
                {
                    table: {
                        headerRows: 1,
                        widths: [70, 100, 80, 70, '*'],
                        body: [
                            [
                                {text: 'Date facture', style: 'tableHeader'},
                                {text: 'Contact', style: 'tableHeader'},
                                {text: 'Règlement', style: 'tableHeader'},
                                {text: 'Échéance', style: 'tableHeader'},
                                {text: 'INCOTERM', style: 'tableHeader'}
                            ],
                            [
                                {text: '26/01/2024', style: 'tableBody'},
                                {text: '', style: 'tableBody'},
                                {text: 'Virement à 60 jours', style: 'tableBody'},
                                {text: '26/03/2024', style: 'tableBody'},
                                {text: 'FCA', style: 'tableBody'}
                            ]
                        ]
                    },
                    layout: {
                        fillColor(rowIndex) {
                            return rowIndex === 0 ? '#A9E2F3' : null
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
                        headerRows: 1,
                        widths: [10, 60, 250, '*', '*', '*'],
                        body: [
                            [{text: '#', style: 'tableHeader'},
                             {text: 'Référence', style: 'tableHeader'},
                             {text: 'Désignation', style: 'tableHeader'},
                             {text: 'P.U. HT', style: 'tableHeader'},
                             {text: 'Quantité', style: 'tableHeader'},
                             {text: 'Prix HT', style: 'tableHeader'}],
                            [
                                '',
                                {colSpan: 5, text: 'BL n° 50711 du 26/01/2024 / Votre référence: 87264123', bold: 'true', style: 'tableBody'},
                                '',
                                '',
                                '',
                                ''
                            ],
                            [
                                {text: '1', style: 'tableBody'},
                                {text: '9813715280', style: 'tableBody'},
                                {text: 'FCA B618 HAB AM FM DAB\nIndice: A\nNuméro de commande: 87264123\nCode Douanier: 8544300089', style: 'tableBody'},
                                {text: '8,61000€', style: 'tableBody'},
                                {text: '480', style: 'tableBody'},
                                {text: '4132,80€', style: 'tableBody'}
                            ],
                            [
                                {text: '2', style: 'tableBody'},
                                {text: '9813715280', style: 'tableBody'},
                                {text: 'FCA B618 HAB AM FM DAB\nIndice: A\nNuméro de commande: 87264123\nCode Douanier: 8544300089', style: 'tableBody'},
                                {text: '8,61000€', style: 'tableBody'},
                                {text: '20', style: 'tableBody'},
                                {text: '172,20€', style: 'tableBody'}
                            ],
                            [
                                '',
                                '',
                                '',
                                '',
                                {text: 'Total HT', style: 'tableBody'},
                                {text: '4305,00€', style: 'tableBody'}
                            ],
                            [
                                '',
                                '',
                                '',
                                '',
                                {text: 'TVA', style: 'tableBody'},
                                {text: '0,00€', style: 'tableBody'}
                            ],
                            [
                                '',
                                '',
                                '',
                                '',
                                {text: 'Total TTC', style: 'tableBody', fillColor: '#A9E2F3'},
                                {text: '4305,00€', style: 'tableBody', fillColor: '#A9E2F3'}
                            ]
                        ]
                    },
                    layout: {
                        fillColor(rowIndex) {
                            return rowIndex === 0 ? '#A9E2F3' : null
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
                    margin: [0, 0, 0, 5]
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
                    margin: [0, 0, 0, 5]
                },
                {
                    table: {
                        widths: ['*', '*'],
                        body: [
                            [
                                {text: 'Ventes intra-communautaire : Exonération de TVA article 262 TER I du CGI.',
                                 border: [false, false, false, false]},
                                {
                                    text: [
                                        {text: 'Coordonnées bancaires:\n', style: 'subheader'},
                                        'BANQUE CIC\n',
                                        'IBAN FR78 3008 7331 2200 0212 7570 107\n',
                                        'BIC CMCIFRPP\n'
                                    ],
                                    fillColor: '#A9E2F3',
                                    border: [false, false, false, false]
                                }
                            ]
                        ]
                    }
                }
            ],

            footer() {
                return {
                    text: 'RCS Vesoul - SIREN 47913401700025 - APE 7112B - TVA Intracommunautaire FR94479134017 - SARL- Capital 1000000€',
                    style: 'footer',
                    alignment: 'center',
                    margin: [0, 10, 0, 0]
                }
            },
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
