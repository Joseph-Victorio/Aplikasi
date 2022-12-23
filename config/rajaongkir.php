<?php

return 
[
  'api_url' => env('RAJAONGKIR_API_URL', 'https://api.rajaongkir.com/starter/'),
  'api_url_basic' => env('RAJAONGKIR_API_BASIC_URL', 'https://api.rajaongkir.com/basic/'),
  'api_url_pro' => env('RAJAONGKIR_API_PRO_URL', 'https://pro.rajaongkir.com/api/'),
  'courier_starter' => 
      [
        ['label' => 'Jalur Eka Nugraha (JNE)', 'value' => 'jne'],
        ['label' => 'Citra Van Titipan Kilat (TIKI)', 'value' => 'tiki'],
        ['label' => 'Pos Indonesia (POS)', 'value' => 'pos']
      ],
      'courier_basic' => 
      [
        ['label' => 'PCP Express (PCP)', 'value' => 'pcp'],
        ['label' => 'RPX Holding (RPX)', 'value' => 'rpx']
      ],
      'courier_pro' =>
      [
        ['label' => 'J&T Express (J&T)', 'value' =>  'jnt'],
        ['label' => 'JET Logistics (JET)', 'value' =>  'jet'],
        ['label' => 'WAHANA Prestasi Logistik (WAHANA)', 'value' =>  'wahana'],
        ['label' => 'ID Express (IDE)', 'value' =>  'ide'], 
        ['label' => 'Ninja Xpress (NINJA)', 'value' =>  'ninja'], 
        ['label' => 'SiCepat Express (SICEPAT)', 'value' =>  'sicepat'], 
        ['label' => 'Anteraja (ANTERAJA)', 'value' =>  'anteraja'], 
        ['label' => 'Satria Antaran Prima (SAP)', 'value' =>  'sap'], 
        ['label' => 'PAHALA Kencana Express (PAHALA)', 'value' =>  'pahala'], 
        ['label' => 'Royal Express Indonesia (REX)', 'value' =>  'rex'], 
        ['label' => 'PANDU Logistics (PANDU)', 'value' =>  'pandu'], 
        ['label' => 'FIRST Logistics (FIRST)', 'value' =>  'first'], 
        ['label' => 'SENTRAL Cargo (SENTRAL)', 'value' =>  'sentral'], 
        ['label' => 'LION Express (LION)', 'value' =>  'lion'], 
        ['label' => 'Solusi Express (SLIS)', 'value' =>  'slis'], 
        ['label' => 'Nusantara Card Semesta (NCS)', 'value' =>  'ncs'], 
        ['label' => '21 Express (DSE)', 'value' =>  'dse'], 
        ['label' => 'IDL Cargo (IDL)', 'value' =>  'idl'], 
        ['label' => 'JTL Express (JTL)', 'value' =>  'jtl'], 
      ]
];