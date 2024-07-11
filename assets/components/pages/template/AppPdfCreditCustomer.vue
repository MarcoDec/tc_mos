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
                                            text: 'Credit Customer\n N° 1\n',
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
                                {text: 'Applicant :\n', style: 'contactInfo'},
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
                                {text: 'To :\n', style: 'contactInfo'},
                                {text: 'ZANINI PARETS SL (13096)\n', style: 'subheader'},
                                {text: 'C/.TENES, 5-9\n', style: 'headerInfo'},
                                {text: 'POL.IND.LLEVANT\n', style: 'headerInfo'},
                                {text: '08 150 PARRETS DEL VALLES\n', style: 'headerInfo'},
                                {text: 'Spain\n', style: 'headerInfo'},
                                {text: 'Tel. +349357377780\n', style: 'contactInfo'},
                                {text: 'ESB61011326\n', style: 'contactInfo', margin: [0, 0, 0, 5]},
                                {text: 'I GOMEZ\n', style: 'headerInfo'},
                                {text: 'igomez@zanini.com', style: 'contactInfo'}
                            ],
                            width: '50%'
                        }
                    ],
                    margin: [0, 0, 0, 10]
                },
                {
                    table: {
                        headerRows: 1,
                        widths: [70, '*'],
                        body: [
                            [
                                {text: 'Date', style: 'tableHeader'},
                                {text: '', style: 'tableHeader'}
                            ],
                            [
                                {text: '26/01/2024', style: 'tableBody'},
                                ''
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
                        widths: [10, 300, '*', '*', '*'],
                        body: [
                            [{text: '#', style: 'tableHeader'},
                             {text: 'Désignation', style: 'tableHeader'},
                             {text: 'Unit price Excl.VAT', style: 'tableHeader'},
                             {text: 'Quantity', style: 'tableHeader'},
                             {text: 'Price excl.VAT', style: 'tableHeader'}],
                            [
                                '',
                                {colSpan: 4, text: 'Avoir sur facture 50524-BL 50537 - Votre référence: 4SR000137/4CC000183', bold: 'true', style: 'tableBody'},
                                '',
                                '',
                                ''
                            ],
                            [
                                {text: '1', style: 'tableBody'},
                                {text: 'Erreur de quantité sur référence: 9852179080', style: 'tableBody'},
                                {text: '-3,06000€', style: 'tableBody'},
                                {text: '500', style: 'tableBody'},
                                {text: '-1530,00€', style: 'tableBody'}
                            ],
                            [
                                '',
                                '',
                                '',
                                {text: 'Total Excl Vat', style: 'tableBody'},
                                {text: '-1530,00€', style: 'tableBody'}
                            ],
                            [
                                '',
                                '',
                                '',
                                {text: 'VAT', style: 'tableBody'},
                                {text: '-0,00€', style: 'tableBody'}
                            ],
                            [
                                '',
                                '',
                                '',
                                {text: 'All tax included', style: 'tableBody', fillColor: '#A9E2F3'},
                                {text: '-1530,00€', style: 'tableBody', fillColor: '#A9E2F3'}
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
                        widths: ['*'],
                        body: [
                            [
                                {text: 'Ventes intra-communautaire : Exonération de TVA article 262 TER I du CGI.',
                                 border: [false, false, false, false]}
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
