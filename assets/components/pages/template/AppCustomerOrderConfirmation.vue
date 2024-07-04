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
                                            text: 'Order confirmation\n N° 17142\n',
                                            alignment: 'center',
                                            style: 'header'
                                        }
                                    ]
                                ]
                            },
                            layout: {
                                fillColor: '#A9E2F3'
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
                            width: '*',
                            text: [
                                {text: 'TCONCEPT\n', style: 'subheader'},
                                '5 rue Alfred Nobel\n',
                                'ZA La charrière\n',
                                '70190 RIOZ\n',
                                'France\n',
                                'Tel. +33 (0)3 84 91 98 84\n',
                                'Fax. +33 (0)3 84 91 98 70\n',
                                'Email: tconcept@orange.fr'
                            ]
                        },
                        {
                            stack: [
                                {text: 'To:', style: 'bold'},
                                'STELLANTIS ESPANA, S.L.',
                                'Av. Citroën 3 y 5',
                                'Zona Franca de Vigo',
                                '36210 VIGO - PONTEVEDRA',
                                'Spain',
                                'Tel.',
                                'ESB36621587'
                            ],
                            width: '50%'
                        }
                    ],
                    margin: [0, 0, 0, 10]
                },
                {
                    text: [
                        {text: 'Delated from  ', fontSize: 7},
                        {text: '03/08/2024 ', fontSize: 9, bold: true},
                        {text: 'we acknowledge receipt of the following order \n ', fontSize: 7}
                    ]
                },
                {
                    table: {
                        headerRows: 1,
                        widths: [150, 150, 70, '*'],
                        body: [
                            [
                                {text: 'Purchaase order number', style: 'tableHeader'},
                                {text: 'Purchaase order date', style: 'tableHeader'},
                                {text: 'Incoterm', style: 'tableHeader'},
                                {text: 'contact', style: 'tableHeader'}
                            ],
                            [
                                '02022024V',
                                '27/03/2024',
                                'FCA',
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
                        }
                    },
                    margin: [0, 10, 0, 10]
                },
                {
                    table: {
                        headerRows: 1,
                        widths: [170, 20, 43, 43, 30, 33, 33, 33, '*'],
                        body: [
                            [
                                {text: 'Item', alignment: 'left', fontSize: 7, bold: true},
                                {text: 'Indice', alignment: 'right', fontSize: 7, bold: true},
                                {text: 'Delivery date requested', alignment: 'right', fontSize: 7, bold: true},
                                {text: 'Delivery date confirmed', alignment: 'right', fontSize: 7, bold: true},
                                {text: 'Unit price Excl.VAT', alignment: 'right', fontSize: 7, bold: true},
                                {text: 'Quantity requested', alignment: 'right', fontSize: 7, bold: true},
                                {text: 'Quantity confirmed', alignment: 'right', fontSize: 7, bold: true},
                                {text: 'Quantity sent', alignment: 'right', fontSize: 7, bold: true},
                                {text: 'Total Excl VAT', alignment: 'right', fontSize: 7, bold: true}
                            ],
                            [
                                {text: [
                                    {text: '9827207860\n', fontSize: 9, color: '#0d6efd', alignment: 'left'},
                                    {text: 'WIRING HARNESS - KFK R2 LAT', style: 'tableBody', alignment: 'left'}
                                ]},
                                {text: '00B', style: 'tableBody'},
                                {text: '02/02/2024', style: 'tableBody'},
                                {text: '02/02/2024', style: 'tableBody'},
                                {text: '4,4000€', style: 'tableBody'},
                                {text: '960', style: 'tableBody'},
                                {text: '960', style: 'tableBody'},
                                {text: '960', style: 'tableBody'},
                                {text: '3878,40€', style: 'tableBody'}
                            ],
                            [
                                {text: [
                                    {text: '9827207860\n', fontSize: 9, color: '#0d6efd', alignment: 'left'},
                                    {text: 'WIRING HARNESS - KFK R2 LAT', style: 'tableBody', alignment: 'left'}
                                ]},
                                {text: '00B', style: 'tableBody'},
                                {text: '02/02/2024', style: 'tableBody'},
                                {text: '02/02/2024', style: 'tableBody'},
                                {text: '4,4000€', style: 'tableBody'},
                                {text: '960', style: 'tableBody'},
                                {text: '960', style: 'tableBody'},
                                {text: '960', style: 'tableBody'},
                                {text: '3878,40€', style: 'tableBody'}
                            ],
                            [
                                {text: [
                                    {text: '9827207860\n', fontSize: 9, color: '#0d6efd', alignment: 'left'},
                                    {text: 'WIRING HARNESS - KFK R2 LAT', style: 'tableBody', alignment: 'left'}
                                ]},
                                {text: '00B', style: 'tableBody'},
                                {text: '02/02/2024', style: 'tableBody'},
                                {text: '02/02/2024', style: 'tableBody'},
                                {text: '4,4000€', style: 'tableBody'},
                                {text: '960', style: 'tableBody'},
                                {text: '960', style: 'tableBody'},
                                {text: '960', style: 'tableBody'},
                                {text: '3878,40€', style: 'tableBody'}
                            ],
                            [
                                {text: [
                                    {text: '9827207860\n', fontSize: 9, color: '#0d6efd', alignment: 'left'},
                                    {text: 'WIRING HARNESS - KFK R2 LAT', style: 'tableBody', alignment: 'left'}
                                ]},
                                {text: '00B', style: 'tableBody'},
                                {text: '02/02/2024', style: 'tableBody'},
                                {text: '02/02/2024', style: 'tableBody'},
                                {text: '4,4000€', style: 'tableBody'},
                                {text: '960', style: 'tableBody'},
                                {text: '960', style: 'tableBody'},
                                {text: '960', style: 'tableBody'},
                                {text: '3878,40€', style: 'tableBody'}
                            ],
                            [
                                {text: [
                                    {text: '9827207860\n', fontSize: 9, color: '#0d6efd', alignment: 'left'},
                                    {text: 'WIRING HARNESS - KFK R2 LAT', style: 'tableBody', alignment: 'left'}
                                ]},
                                {text: '00B', style: 'tableBody'},
                                {text: '02/02/2024', style: 'tableBody'},
                                {text: '02/02/2024', style: 'tableBody'},
                                {text: '4,4000€', style: 'tableBody'},
                                {text: '960', style: 'tableBody'},
                                {text: '960', style: 'tableBody'},
                                {text: '960', style: 'tableBody'},
                                {text: '3878,40€', style: 'tableBody'}
                            ],
                            [
                                {text: [
                                    {text: '9827207860\n', fontSize: 9, color: '#0d6efd', alignment: 'left'},
                                    {text: 'WIRING HARNESS - KFK R2 LAT', style: 'tableBody', alignment: 'left'}
                                ]},
                                {text: '00B', style: 'tableBody'},
                                {text: '02/02/2024', style: 'tableBody'},
                                {text: '02/02/2024', style: 'tableBody'},
                                {text: '4,4000€', style: 'tableBody'},
                                {text: '960', style: 'tableBody'},
                                {text: '960', style: 'tableBody'},
                                {text: '960', style: 'tableBody'},
                                {text: '3878,40€', style: 'tableBody'}
                            ],
                            [
                                {text: [
                                    {text: '9827207860\n', fontSize: 9, color: '#0d6efd', alignment: 'left'},
                                    {text: 'WIRING HARNESS - KFK R2 LAT', style: 'tableBody', alignment: 'left'}
                                ]},
                                {text: '00B', style: 'tableBody'},
                                {text: '02/02/2024', style: 'tableBody'},
                                {text: '02/02/2024', style: 'tableBody'},
                                {text: '4,4000€', style: 'tableBody'},
                                {text: '960', style: 'tableBody'},
                                {text: '960', style: 'tableBody'},
                                {text: '960', style: 'tableBody'},
                                {text: '3878,40€', style: 'tableBody'}
                            ],
                            [
                                {text: [
                                    {text: '9827207860\n', fontSize: 9, color: '#0d6efd', alignment: 'left'},
                                    {text: 'WIRING HARNESS - KFK R2 LAT', style: 'tableBody', alignment: 'left'}
                                ]},
                                {text: '00B', style: 'tableBody'},
                                {text: '02/02/2024', style: 'tableBody'},
                                {text: '02/02/2024', style: 'tableBody'},
                                {text: '4,4000€', style: 'tableBody'},
                                {text: '960', style: 'tableBody'},
                                {text: '960', style: 'tableBody'},
                                {text: '960', style: 'tableBody'},
                                {text: '3878,40€', style: 'tableBody'}
                            ],
                            [
                                {text: [
                                    {text: '9827207860\n', fontSize: 9, color: '#0d6efd', alignment: 'left'},
                                    {text: 'WIRING HARNESS - KFK R2 LAT', style: 'tableBody', alignment: 'left'}
                                ]},
                                {text: '00B', style: 'tableBody'},
                                {text: '02/02/2024', style: 'tableBody'},
                                {text: '02/02/2024', style: 'tableBody'},
                                {text: '4,4000€', style: 'tableBody'},
                                {text: '960', style: 'tableBody'},
                                {text: '960', style: 'tableBody'},
                                {text: '960', style: 'tableBody'},
                                {text: '3878,40€', style: 'tableBody'}
                            ],
                            [
                                {text: [
                                    {text: '9827207860\n', fontSize: 9, color: '#0d6efd', alignment: 'left'},
                                    {text: 'WIRING HARNESS - KFK R2 LAT', style: 'tableBody', alignment: 'left'}
                                ]},
                                {text: '00B', style: 'tableBody'},
                                {text: '02/02/2024', style: 'tableBody'},
                                {text: '02/02/2024', style: 'tableBody'},
                                {text: '4,4000€', style: 'tableBody'},
                                {text: '960', style: 'tableBody'},
                                {text: '960', style: 'tableBody'},
                                {text: '960', style: 'tableBody'},
                                {text: '3878,40€', style: 'tableBody'}
                            ],
                            [
                                {text: [
                                    {text: '9827207860\n', fontSize: 9, color: '#0d6efd', alignment: 'left'},
                                    {text: 'WIRING HARNESS - KFK R2 LAT', style: 'tableBody', alignment: 'left'}
                                ]},
                                {text: '00B', style: 'tableBody'},
                                {text: '02/02/2024', style: 'tableBody'},
                                {text: '02/02/2024', style: 'tableBody'},
                                {text: '4,4000€', style: 'tableBody'},
                                {text: '960', style: 'tableBody'},
                                {text: '960', style: 'tableBody'},
                                {text: '960', style: 'tableBody'},
                                {text: '3878,40€', style: 'tableBody'}
                            ],
                            [
                                {text: [
                                    {text: '9827207860\n', fontSize: 9, color: '#0d6efd', alignment: 'left'},
                                    {text: 'WIRING HARNESS - KFK R2 LAT', style: 'tableBody', alignment: 'left'}
                                ]},
                                {text: '00B', style: 'tableBody'},
                                {text: '02/02/2024', style: 'tableBody'},
                                {text: '02/02/2024', style: 'tableBody'},
                                {text: '4,4000€', style: 'tableBody'},
                                {text: '960', style: 'tableBody'},
                                {text: '960', style: 'tableBody'},
                                {text: '960', style: 'tableBody'},
                                {text: '3878,40€', style: 'tableBody'}
                            ],
                            [
                                {text: [
                                    {text: '9827207860\n', fontSize: 9, color: '#0d6efd', alignment: 'left'},
                                    {text: 'WIRING HARNESS - KFK R2 LAT', style: 'tableBody', alignment: 'left'}
                                ]},
                                {text: '00B', style: 'tableBody'},
                                {text: '02/02/2024', style: 'tableBody'},
                                {text: '02/02/2024', style: 'tableBody'},
                                {text: '4,4000€', style: 'tableBody'},
                                {text: '960', style: 'tableBody'},
                                {text: '960', style: 'tableBody'},
                                {text: '960', style: 'tableBody'},
                                {text: '3878,40€', style: 'tableBody'}
                            ],
                            [
                                {text: [
                                    {text: '9827207860\n', fontSize: 9, color: '#0d6efd', alignment: 'left'},
                                    {text: 'WIRING HARNESS - KFK R2 LAT', style: 'tableBody', alignment: 'left'}
                                ]},
                                {text: '00B', style: 'tableBody'},
                                {text: '02/02/2024', style: 'tableBody'},
                                {text: '02/02/2024', style: 'tableBody'},
                                {text: '4,4000€', style: 'tableBody'},
                                {text: '960', style: 'tableBody'},
                                {text: '960', style: 'tableBody'},
                                {text: '960', style: 'tableBody'},
                                {text: '3878,40€', style: 'tableBody'}
                            ],
                            ['', '', '', '', '', '', '', '', {text: '13878,40€', style: 'totalTableBody'}]
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
                        }
                    },
                    margin: [0, 30, 0, 30]
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
                    margin: [0, 0, 0, 10] // marges pour l'espacement
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
                    margin: [0, 0, 0, 10] // marges pour l'espacement
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
                    margin: [0, 0, 0, 10] // marges pour l'espacement
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
                    fontSize: 14,
                    bold: true
                },
                tableHeader: {
                    bold: true,
                    fontSize: 12,
                    color: 'black'
                },
                footer: {
                    italics: true,
                    fontSize: 8
                },
                pagination: {
                    fontSize: 14,
                    bold: true
                },
                tableBody: {
                    fontSize: 8,
                    alignment: 'right'
                },
                totalTableBody: {
                    fontSize: 8,
                    alignment: 'right',
                    bold: true
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
