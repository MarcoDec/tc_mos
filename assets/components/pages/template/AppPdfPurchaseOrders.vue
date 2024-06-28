<script setup>
import { usePDF } from 'vue3-pdfmake';
import logo from './img/TConcept_Logo.png';

// Convertir le logo en base64
const toBase64 = (url) => {
  return new Promise((resolve, reject) => {
    const xhr = new XMLHttpRequest();
    xhr.onload = () => {
      const reader = new FileReader();
      reader.onloadend = () => {
        resolve(reader.result);
      };
      reader.readAsDataURL(xhr.response);
    };
    xhr.onerror = () => {
      reject(xhr.statusText);
    };
    xhr.open('GET', url);
    xhr.responseType = 'blob';
    xhr.send();
  });
};

const pdfmake = usePDF({
  autoInstallVFS: true
});

const onGenPDF = async () => {
  const logoBase64 = await toBase64(logo);

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
                    text: 'ORDER\nN° 500363',
                    alignment: 'center',
                    style: 'header'
                  }
                ]
              ]
            },
            layout: {
              fillColor: '#999'
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
              { text: 'TCONCEPT\n', style: 'subheader' },
              '5 rue Alfred Nobel\n',
              'ZA La charrière\n',
              '70190 RIOZ\n',
              'France\n',
              'Tel. +33 (0)3 84 91 98 84\n',
              'Fax. +33 (0)3 84 91 98 70\n'
            ]
          },
          {
            table: {
              widths: ['*'],
              body: [
                [
                  {
                    text: [
                      { text: 'MD ELEKTRONIK gmbh\n', style: 'subheader' },
                      'Neutraublinger Str 4\n',
                      '84478 Waldkraiburg\n',
                      'Germany\n',
                      'Tel. 498638604488\n'
                    ],
  
                  }
                ]
              ]
            },
            layout: {
              fillColor: '#e9ecef',
              hLineWidth: function(i, node) {
                return 0.1; // épaisseur de ligne horizontale très fine
              },
              vLineWidth: function(i, node) {
                return 0.1; // épaisseur de ligne verticale très fine
              },
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
        text: '1/1',
        style: 'pagination',
        margin: [60, 0, 0, 0]
      },
      {
        table: {
          headerRows: 1,
          widths: [55, 50, 50, 50, 170 , '*'],
          body: [
            [
              { text: 'Date', style: 'tableHeader' },
              { text: 'Contact person', style: 'tableHeader' },
              { text: 'Customer n°', style: 'tableHeader' },
              { text: 'Terms of payment', style: 'tableHeader' },
              { text: 'Info', style: 'tableHeader' },
              { text: 'Delivery', style: 'tableHeader' }
            ],
            [
              '27/03/2024',
              'Admin5 super',
              '',
              '',
              'Please send your invoices to: supplier-invoices@tconcept.fr',
              {
                text: [
                  'TCONCEPT\n',
                  '5 rue Alfred Nobel\n',
                  'ZA La charrière\n',
                  '70190 RIOZ\n',
                  'France\n',
                  'Tel. +33 (0)3 84 91 98 84\n'
                ]
              }
            ]
          ]
        },
        layout: {
          fillColor: function (rowIndex, node, columnIndex) {
            return (rowIndex % 2 === 0) ? '#e9ecef' : null;
          },
          hLineWidth: function(i, node) {
            return 0.01; // épaisseur de ligne horizontale très fine
          },
          vLineWidth: function(i, node) {
            return 0.01; // épaisseur de ligne verticale très fine
          },
        },
        margin: [0, 10, 0, 10]
      },
      {
        table: {
          headerRows: 1,
          widths: [10, '*', '*', '*', '*' , 30, '*' ,'*'],
          body: [
            [
              { text: 'P', style: 'tableHeader' },
              { text: 'Part number', style: 'tableHeader' },
              { text: 'Designation', style: 'tableHeader' },
              { text: 'Requested delivery date', style: 'tableHeader' },
              { text: 'Quantity', style: 'tableHeader' },
              { text: 'Unit', style: 'tableHeader' },
              { text: 'Unit price Excl.VAT', style: 'tableHeader' },
              { text: 'Total Excl VAT', style: 'tableHeader' }
            ],
            [
              '1',
              '393333',
              'CABLE COAX1XL 1543 FB/M/B',
              '05/03/2024',
              '1200',
              'U',
              '1,3253€',
              '1590,43€'
            ],
            [
              '2',
              '393334',
              'CABLE COAX2L 1520 FM/MM',
              '05/03/2024',
              '1200',
              'U',
              '1,3191€',
              '1583,86€'
            ],
            [
              '3',
              '393337',
              'CABLE COAX1XL 995 FC/FC',
              '26/03/2024',
              '20000',
              'U',
              '1,1971€',
              '23943,20€'
            ],
            [
              { text: '', margin: [0, 100, 0, 80] }, {}, {}, {}, {}, {}, {}, {}
            ]
          ]
        },
        layout: {
          fillColor: function (rowIndex, node, columnIndex) {
            return (rowIndex % 2 === 0) ? '#e9ecef' : null;
          },
          hLineWidth: function(i, node) {
            return 0.01; // épaisseur de ligne horizontale très fine
          },
          vLineWidth: function(i, node) {
            return 0.01; // épaisseur de ligne verticale très fine
          }
        },
        margin: [0, 10, 0, 10]
      },
      {
        table: {
          widths: [400,'*'],
          body: [
            [
              'CONFIRMATION OBLIGATOIRE SOUS 48H PAR AR', 
              {
                text: 'TOTAL EXCL. VAT\n27117,53€',
                alignment: 'center',
                margin: [0, 10, 0, 0]
              }
            ]
          ]
        }
      }
    ],
    footer: function(currentPage, pageCount) {
      return {
        text: 'RCS Vesoul - SIREN 47913401700025 - APE 7112B - TVA Intracommunautaire FR94479134017 - SARL- Capital 1000000€',
        style: 'footer',
        alignment: 'center',
        margin: [0, 10, 0, 0]
      };
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
        bold: true,
      },
    },
    defaultStyle: {
      fontSize: 10
    }
  }).open();
}
</script>

<template>
  <button @click="onGenPDF">Click here to download demo PDF</button>
</template>
