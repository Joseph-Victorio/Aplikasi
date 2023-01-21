<?php

return 
[
  'api_url' => env('RAJAONGKIR_API_URL', 'https://api.rajaongkir.com/starter/'),
  'api_url_basic' => env('RAJAONGKIR_API_BASIC_URL', 'https://api.rajaongkir.com/basic/'),
  'api_url_pro' => env('RAJAONGKIR_API_PRO_URL', 'https://pro.rajaongkir.com/api/'),
  'courier_starter' => 
      [
        ['label' => 'JNE', 'value' => 'jne'],
        ['label' => 'TIKI', 'value' => 'tiki'],
        ['label' => 'POS', 'value' => 'pos']
      ],
      'courier_basic' => 
      [
        ['label' => 'PCP Express', 'value' => 'pcp'],
        ['label' => 'RPX Holding', 'value' => 'rpx']
      ],
      'courier_pro' =>
      [
        ['label' => 'J&T Express', 'value' =>  'jnt'],
        ['label' => 'JET Logistics', 'value' =>  'jet'],
        ['label' => 'WAHANA (Wahana Prestasi Logistik)', 'value' =>  'wahana'],
        ['label' => 'IDE (ID Express)', 'value' =>  'ide'], 
        ['label' => 'Ninja Xpress', 'value' =>  'ninja'], 
        ['label' => 'SICEPAT Express', 'value' =>  'sicepat'], 
        ['label' => 'ANTERAJA', 'value' =>  'anteraja'], 
        ['label' => 'SAP (Satria Antaran Prima)', 'value' =>  'sap'], 
        ['label' => 'PAHALA (Pahala Kencana Express)', 'value' =>  'pahala'], 
        ['label' => 'REX (Royal Express Indonesia)', 'value' =>  'rex'], 
        ['label' => 'PANDU Logistics', 'value' =>  'pandu'], 
        ['label' => 'FIRST Logistics', 'value' =>  'first'], 
        ['label' => 'SENTRAL Cargo', 'value' =>  'sentral'], 
        ['label' => 'LION Express', 'value' =>  'lion'], 
        ['label' => 'SLIS (Solusi Express)', 'value' =>  'slis'], 
        ['label' => 'NCS (Nusantara Card Semesta)', 'value' =>  'ncs'], 
        ['label' => 'DSE (21 Express)', 'value' =>  'dse'], 
        ['label' => 'IDL Cargo', 'value' =>  'idl'], 
        ['label' => 'JTL Express', 'value' =>  'jtl'], 
      ]
];