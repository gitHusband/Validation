<?php

namespace githusband\Tests\Rule;

/**
 * The default timezone is 'Asia/Shanghai', {@see tests/Test.php}
 */
trait TestRuledatetime
{
    protected function test_method_datetime()
    {
        $rules = [
            "symbol" => [
                "A_datetime" => "optional|datetime",
                "B_datetime" => "optional|datetime[Y-m-d]",
                "C_datetime" => "optional|datetime[Y/m/d]",
                "D_datetime" => "optional|datetime[Y/m-d]",
                "E_datetime" => "optional|datetime[d/m/Y]",
                "F_datetime" => "optional|datetime[Y-m-d H:i:s]",
                "G_datetime" => "optional|datetime[Y/m/d H:i:s]",
                "H_datetime" => "optional|datetime[YmdHis]",
                "I_datetime" => "optional|datetime[ATOM]",
                "J_datetime" => "optional|datetime[COOKIE]",
                "K_datetime" => "optional|datetime[RFC3339]",
                // "L_datetime" => "optional|datetime[RFC3339_EXTENDED]", // PHP 7+
                "M_datetime" => "optional|datetime[W3C]",
            ],
            "method" => [
                "A_datetime" => "optional|is_datetime",
                "B_datetime" => "optional|is_datetime[Y-m-d]",
                "C_datetime" => "optional|is_datetime[Y/m/d]",
                "D_datetime" => "optional|is_datetime[Y/m-d]",
                "E_datetime" => "optional|is_datetime[d/m/Y]",
                "F_datetime" => "optional|is_datetime[Y-m-d H:i:s]",
                "G_datetime" => "optional|is_datetime[Y/m/d H:i:s]",
                "H_datetime" => "optional|is_datetime[YmdHis]",
                "I_datetime" => "optional|is_datetime[ATOM]",
                "J_datetime" => "optional|is_datetime[COOKIE]",
                "K_datetime" => "optional|is_datetime[RFC3339]",
                // "L_datetime" => "optional|is_datetime[RFC3339_EXTENDED]", // PHP 7+
                "M_datetime" => "optional|is_datetime[W3C]",
            ],
        ];

        $cases = [
            "Valid_A_datetime_1" => [
                "data" => [
                    "A_datetime" => "20240423",
                ]
            ],
            "Valid_A_datetime_2" => [
                "data" => [
                    "A_datetime" => '2024-04-23',
                ]
            ],
            "Valid_A_datetime_3" => [
                "data" => [
                    "A_datetime" => '04/23/2024',
                ]
            ],
            "Valid_B_datetime_1" => [
                "data" => [
                    "B_datetime" => '2024-04-23',
                ]
            ],
            "Valid_C_datetime_1" => [
                "data" => [
                    "C_datetime" => '2024/04/23',
                ]
            ],
            "Valid_D_datetime_1" => [
                "data" => [
                    "D_datetime" => '2024/04-23',
                ]
            ],
            "Valid_E_datetime_1" => [
                "data" => [
                    "E_datetime" => '23/04/2024',
                ]
            ],
            "Valid_F_datetime_1" => [
                "data" => [
                    "F_datetime" => '2024-04-23 12:00:00',
                ]
            ],
            "Valid_G_datetime_1" => [
                "data" => [
                    "G_datetime" => '2024/04/23 12:00:00',
                ]
            ],
            "Valid_H_datetime_1" => [
                "data" => [
                    "H_datetime" => '20240423000000',
                ]
            ],
            "Valid_I_datetime_1" => [
                "data" => [
                    "I_datetime" => '2024-04-23T12:00:00+08:00',
                ]
            ],
            "Valid_J_datetime_1" => [
                "data" => [
                    "J_datetime" => 'Wed, 15-Apr-2024 12:00:00 UTC',
                ]
            ],
            "Valid_K_datetime_1" => [
                "data" => [
                    "K_datetime" => '2024-04-23T12:00:00+08:00',
                ]
            ],
            // "Valid_L_datetime_1" => [
            //     "data" => [
            //         "L_datetime" => '2024-04-23T12:00:00.000+08:00',
            //     ]
            // ],
            "Valid_M_datetime_1" => [
                "data" => [
                    "M_datetime" => '2024-04-23T12:00:00+08:00',
                ]
            ],

            /**
             * Invalid cases
             */
            "Invalid_A_datetime_1" => [
                "data" => [
                    "A_datetime" => "12345678",
                ],
                "expected_msg" => ["A_datetime" => "A_datetime must be a valid datetime"]
            ],
            "Invalid_A_datetime_2" => [
                "data" => [
                    "A_datetime" => "23/04/2024",
                ],
                "expected_msg" => ["A_datetime" => "A_datetime must be a valid datetime"]
            ],
            "Invalid_A_datetime_3" => [
                "data" => [
                    "A_datetime" => "2024-04-50",
                ],
                "expected_msg" => ["A_datetime" => "A_datetime must be a valid datetime"]
            ],
            "Invalid_B_datetime_1" => [
                "data" => [
                    "B_datetime" => "2024-04-50",
                ],
                "expected_msg" => ["B_datetime" => "B_datetime must be a valid datetime in format Y-m-d"]
            ],
            "Invalid_B_datetime_2" => [
                "data" => [
                    "B_datetime" => "2024/04/23",
                ],
                "expected_msg" => ["B_datetime" => "B_datetime must be a valid datetime in format Y-m-d"]
            ],
            "Invalid_C_datetime_1" => [
                "data" => [
                    "C_datetime" => "2024/04/50",
                ],
                "expected_msg" => ["C_datetime" => "C_datetime must be a valid datetime in format Y/m/d"]
            ],
            "Invalid_C_datetime_2" => [
                "data" => [
                    "C_datetime" => "2024/04-23",
                ],
                "expected_msg" => ["C_datetime" => "C_datetime must be a valid datetime in format Y/m/d"]
            ],
            "Invalid_D_datetime_1" => [
                "data" => [
                    "D_datetime" => "2024/04-50",
                ],
                "expected_msg" => ["D_datetime" => "D_datetime must be a valid datetime in format Y/m-d"]
            ],
            "Invalid_D_datetime_2" => [
                "data" => [
                    "D_datetime" => "2024-04-23",
                ],
                "expected_msg" => ["D_datetime" => "D_datetime must be a valid datetime in format Y/m-d"]
            ],
            "Invalid_E_datetime_1" => [
                "data" => [
                    "E_datetime" => "50/04/2024",
                ],
                "expected_msg" => ["E_datetime" => "E_datetime must be a valid datetime in format d/m/Y"]
            ],
            "Invalid_E_datetime_2" => [
                "data" => [
                    "E_datetime" => "2024-04-23",
                ],
                "expected_msg" => ["E_datetime" => "E_datetime must be a valid datetime in format d/m/Y"]
            ],
            "Invalid_F_datetime_1" => [
                "data" => [
                    "F_datetime" => "2024-04-23 23:60:00",
                ],
                "expected_msg" => ["F_datetime" => "F_datetime must be a valid datetime in format Y-m-d H:i:s"]
            ],
            "Invalid_F_datetime_2" => [
                "data" => [
                    "F_datetime" => "2024-04-23+23:59:59",
                ],
                "expected_msg" => ["F_datetime" => "F_datetime must be a valid datetime in format Y-m-d H:i:s"]
            ],
            "Invalid_G_datetime_1" => [
                "data" => [
                    "G_datetime" => "2024/04/23 23:60:00",
                ],
                "expected_msg" => ["G_datetime" => "G_datetime must be a valid datetime in format Y/m/d H:i:s"]
            ],
            "Invalid_G_datetime_2" => [
                "data" => [
                    "G_datetime" => "2024/04/23+23:59:59",
                ],
                "expected_msg" => ["G_datetime" => "G_datetime must be a valid datetime in format Y/m/d H:i:s"]
            ],
            "Invalid_H_datetime_1" => [
                "data" => [
                    "H_datetime" => "20240423236000",
                ],
                "expected_msg" => ["H_datetime" => "H_datetime must be a valid datetime in format YmdHis"]
            ],
            "Invalid_H_datetime_2" => [
                "data" => [
                    "H_datetime" => "202404232359590",
                ],
                "expected_msg" => ["H_datetime" => "H_datetime must be a valid datetime in format YmdHis"]
            ],
            "Invalid_I_datetime_1" => [
                "data" => [
                    "I_datetime" => '2024-04-23T25:00:00+08:00',
                ],
                "expected_msg" => ["I_datetime" => "I_datetime must be a valid datetime in format ATOM"]
            ],
            "Invalid_I_datetime_2" => [
                "data" => [
                    "I_datetime" => '2024-04-23 23:00:00+08:00',
                ],
                "expected_msg" => ["I_datetime" => "I_datetime must be a valid datetime in format ATOM"]
            ],
            "Invalid_J_datetime_1" => [
                "data" => [
                    "J_datetime" => 'Wed 15-Apr-2024 15:52:01 UTC',
                ],
                "expected_msg" => ["J_datetime" => "J_datetime must be a valid datetime in format COOKIE"]
            ],
            "Invalid_J_datetime_2" => [
                "data" => [
                    "J_datetime" => 'Wed, 50-Apr-2024 15:52:01 UTC',
                ],
                "expected_msg" => ["J_datetime" => "J_datetime must be a valid datetime in format COOKIE"]
            ],
            "Invalid_K_datetime_1" => [
                "data" => [
                    "K_datetime" => '2024-04-23T25:00:00+08:00',
                ],
                "expected_msg" => ["K_datetime" => "K_datetime must be a valid datetime in format RFC3339"]
            ],
            "Invalid_K_datetime_2" => [
                "data" => [
                    "K_datetime" => '2024-04-23 23:00:00+08:00',
                ],
                "expected_msg" => ["K_datetime" => "K_datetime must be a valid datetime in format RFC3339"]
            ],
            // "Invalid_L_datetime_1" => [
            //     "data" => [
            //         "L_datetime" => '2024-04-23T25:00:00.000+08:00',
            //     ],
            //     "expected_msg" => ["L_datetime" => "L_datetime must be a valid datetime in format RFC3339_EXTENDED"]
            // ],
            // "Invalid_L_datetime_2" => [
            //     "data" => [
            //         "L_datetime" => '2024-04-23T23:00:00.0001+08:00',
            //     ],
            //     "expected_msg" => ["L_datetime" => "L_datetime must be a valid datetime in format RFC3339_EXTENDED"]
            // ],
            "Invalid_M_datetime_1" => [
                "data" => [
                    "M_datetime" => '2024-04-23T25:00:0008:00',
                ],
                "expected_msg" => ["M_datetime" => "M_datetime must be a valid datetime in format W3C"]
            ],
            "Invalid_M_datetime_2" => [
                "data" => [
                    "M_datetime" => '2024-04-23 23:00:00+08:00',
                ],
                "expected_msg" => ["M_datetime" => "M_datetime must be a valid datetime in format W3C"]
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
        ];

        return $method_info = [
            "rules" => $rules,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_datetime_equal()
    {
        $rules = [
            "symbol" => [
                "A_datetime" => "optional|datetime=[2024-04-23]",
                "B_datetime" => "optional|datetime=[2024-04-23,Y-m-d]",
                "C_datetime" => "optional|datetime=[2024-04-23,d/Y/m]",
                "D_datetime" => "optional|datetime=[2024-04-23 01,Y-m-d H]",
                "E_datetime" => "optional|datetime=[2024-04-23 01:01,Y-m-d H:i]",
                "F_datetime" => "optional|datetime=[2024-04-23 01:01:01,Y-m-d H:i:s]",
                "G_datetime" => "optional|datetime=[2024-04-23 01:01,Y/m/d H:i]",
                "H_datetime" => "optional|datetime=[2024-04-23 01:01:01,Y/m/d H:i:s]",
                "I_datetime" => "optional|datetime=[2024-04-23T01:01:01+08:00,RFC3339]",
                "J_datetime" => "optional|datetime=[2024-04-23 01:01:01,RFC3339]",  // The default timezone is 'Asia/Shanghai', {@see tests/Test.php}
                "K_datetime" => "optional|datetime=[Tue\, 23-Apr-2024 01:01:01 Asia/Shanghai,COOKIE]",
                "L_datetime" => "optional|datetime=[2024-04-23 01:01:01,COOKIE]",   // The default timezone is 'Asia/Shanghai', {@see tests/Test.php}
                "A_exc" => "optional|datetime=[2024-04-50]",
                "C_exc" => "optional|datetime=[2024-13-23,d/Y/m]",
                "F_exc" => "optional|datetime=[2024-04-23 25:01:01,Y-m-d H:i:s]",
                "I_exc" => "optional|datetime=[2024-04-23T01:0101+08:00,RFC3339]",
                "K_exc" => "optional|datetime=[Tue\, 23-Apri-2024 01:01:01 Asia/Shanghai,COOKIE]",
            ],
            "method" => [
                "A_datetime" => "optional|datetime_equal[2024-04-23]",
                "B_datetime" => "optional|datetime_equal[2024-04-23,Y-m-d]",
                "C_datetime" => "optional|datetime_equal[2024-04-23,d/Y/m]",
                "D_datetime" => "optional|datetime_equal[2024-04-23 01,Y-m-d H]",
                "E_datetime" => "optional|datetime_equal[2024-04-23 01:01,Y-m-d H:i]",
                "F_datetime" => "optional|datetime_equal[2024-04-23 01:01:01,Y-m-d H:i:s]",
                "G_datetime" => "optional|datetime_equal[2024-04-23 01:01,Y/m/d H:i]",
                "H_datetime" => "optional|datetime_equal[2024-04-23 01:01:01,Y/m/d H:i:s]",
                "I_datetime" => "optional|datetime_equal[2024-04-23T01:01:01+08:00,RFC3339]",
                "J_datetime" => "optional|datetime_equal[2024-04-23 01:01:01,RFC3339]",  // The default timezone is 'Asia/Shanghai', {@see tests/Test.php}
                "K_datetime" => "optional|datetime_equal[Tue\, 23-Apr-2024 01:01:01 Asia/Shanghai,COOKIE]",
                "L_datetime" => "optional|datetime_equal[2024-04-23 01:01:01,COOKIE]",   // The default timezone is 'Asia/Shanghai', {@see tests/Test.php}
                "A_exc" => "optional|datetime_equal[2024-04-50]",
                "A_exc" => "optional|datetime_equal[2024-04-50]",
                "C_exc" => "optional|datetime_equal[2024-13-23,d/Y/m]",
                "F_exc" => "optional|datetime_equal[2024-04-23 25:01:01,Y-m-d H:i:s]",
                "I_exc" => "optional|datetime_equal[2024-04-23T01:0101+08:00,RFC3339]",
                "K_exc" => "optional|datetime_equal[Tue\, 23-Apri-2024 01:01:01 Asia/Shanghai,COOKIE]",
            ]
        ];

        $cases = [
            "Valid_A_datetime_1" => [
                "data" => [
                    "A_datetime" => "20240423",
                ]
            ],
            "Valid_A_datetime_2" => [
                "data" => [
                    "A_datetime" => '2024-04-23',
                ]
            ],
            "Valid_A_datetime_3" => [
                "data" => [
                    "A_datetime" => '04/23/2024',
                ]
            ],
            "Valid_B_datetime_1" => [
                "data" => [
                    "B_datetime" => '2024-04-23',
                ]
            ],
            "Valid_C_datetime_1" => [
                "data" => [
                    "C_datetime" => '23/2024/04',
                ]
            ],
            "Valid_D_datetime_1" => [
                "data" => [
                    "D_datetime" => '2024-04-23 01',
                ]
            ],
            "Valid_D_datetime_2" => [
                "data" => [
                    "D_datetime" => '2024-04-23 1',
                ]
            ],
            "Valid_E_datetime_1" => [
                "data" => [
                    "E_datetime" => '2024-04-23 01:01',
                ]
            ],
            "Valid_E_datetime_1" => [
                "data" => [
                    "E_datetime" => '2024-04-23 01:01',
                ]
            ],
            "Valid_F_datetime_1" => [
                "data" => [
                    "F_datetime" => '2024-04-23 01:01:01',
                ]
            ],
            "Valid_G_datetime_1" => [
                "data" => [
                    "G_datetime" => '2024/04/23 01:01',
                ]
            ],
            "Valid_H_datetime_1" => [
                "data" => [
                    "H_datetime" => '2024/04/23 01:01:01',
                ]
            ],
            "Valid_I_datetime_1" => [
                "data" => [
                    "I_datetime" => '2024-04-23T01:01:01+08:00',
                ]
            ],
            "Valid_I_datetime_2" => [
                "data" => [
                    "I_datetime" => '2024-04-22T17:01:01+00:00',
                ]
            ],
            "Valid_J_datetime_1" => [
                "data" => [
                    "J_datetime" => '2024-04-23T01:01:01+08:00',
                ]
            ],
            "Valid_J_datetime_2" => [
                "data" => [
                    "J_datetime" => '2024-04-22T17:01:01+00:00',
                ]
            ],
            "Valid_K_datetime_1" => [
                "data" => [
                    "K_datetime" => 'Tue, 23-Apr-2024 01:01:01 Asia/Shanghai',
                ]
            ],
            "Valid_K_datetime_2" => [
                "data" => [
                    "K_datetime" => 'Mon, 22-Apr-2024 17:01:01 UTC',
                ]
            ],
            "Valid_L_datetime_1" => [
                "data" => [
                    "L_datetime" => 'Tue, 23-Apr-2024 01:01:01 Asia/Shanghai',
                ]
            ],
            "Valid_L_datetime_2" => [
                "data" => [
                    "L_datetime" => 'Mon, 22-Apr-2024 17:01:01 UTC',
                ]
            ],

            /**
             * Invalid cases
             */
            "Invalid_A_datetime_1" => [
                "data" => [
                    "A_datetime" => "12345678",
                ],
                "expected_msg" => ["A_datetime" => "A_datetime must be a valid datetime"]
            ],
            "Invalid_A_datetime_2" => [
                "data" => [
                    "A_datetime" => "23/04/2024",
                ],
                "expected_msg" => ["A_datetime" => "A_datetime must be a valid datetime"]
            ],
            "Invalid_A_datetime_3" => [
                "data" => [
                    "A_datetime" => "2024-04-50",
                ],
                "expected_msg" => ["A_datetime" => "A_datetime must be a valid datetime"]
            ],
            "Invalid_A_datetime_4" => [
                "data" => [
                    "A_datetime" => "2024-05-01",
                ],
                "expected_msg" => ["A_datetime" => "A_datetime must be a valid datetime and equal to 2024-04-23"]
            ],
            "Invalid_A_datetime_5" => [
                "data" => [
                    "A_datetime" => "2024/05/01",
                ],
                "expected_msg" => ["A_datetime" => "A_datetime must be a valid datetime and equal to 2024-04-23"]
            ],
            "Invalid_A_datetime_6" => [
                "data" => [
                    "A_datetime" => "05/01/2024",
                ],
                "expected_msg" => ["A_datetime" => "A_datetime must be a valid datetime and equal to 2024-04-23"]
            ],
            "Invalid_B_datetime_1" => [
                "data" => [
                    "B_datetime" => "2024/04/23",
                ],
                "expected_msg" => ["B_datetime" => "B_datetime must be a valid datetime in format Y-m-d"]
            ],
            "Invalid_B_datetime_2" => [
                "data" => [
                    "B_datetime" => "2024-04-23 01:00:00",
                ],
                "expected_msg" => ["B_datetime" => "B_datetime must be a valid datetime in format Y-m-d"]
            ],
            "Invalid_B_datetime_3" => [
                "data" => [
                    "B_datetime" => "2024-04-24",
                ],
                "expected_msg" => ["B_datetime" => "B_datetime must be a valid datetime and equal to 2024-04-23"]
            ],
            "Invalid_C_datetime_1" => [
                "data" => [
                    "C_datetime" => "2024/04/23",
                ],
                "expected_msg" => ["C_datetime" => "C_datetime must be a valid datetime in format d/Y/m"]
            ],
            "Invalid_C_datetime_2" => [
                "data" => [
                    "C_datetime" => "2024-04-23 01:00:00",
                ],
                "expected_msg" => ["C_datetime" => "C_datetime must be a valid datetime in format d/Y/m"]
            ],
            "Invalid_C_datetime_3" => [
                "data" => [
                    "C_datetime" => "24/2024/04",
                ],
                "expected_msg" => ["C_datetime" => "C_datetime must be a valid datetime and equal to 2024-04-23"]
            ],
            "Invalid_D_datetime_1" => [
                "data" => [
                    "D_datetime" => "2024-04-23 25",
                ],
                "expected_msg" => ["D_datetime" => "D_datetime must be a valid datetime in format Y-m-d H"]
            ],
            "Invalid_D_datetime_2" => [
                "data" => [
                    "D_datetime" => "2024-04-23 02",
                ],
                "expected_msg" => ["D_datetime" => "D_datetime must be a valid datetime and equal to 2024-04-23 01"]
            ],
            "Invalid_D_datetime_3" => [
                "data" => [
                    "D_datetime" => "2024-04-24 01",
                ],
                "expected_msg" => ["D_datetime" => "D_datetime must be a valid datetime and equal to 2024-04-23 01"]
            ],
            "Invalid_E_datetime_1" => [
                "data" => [
                    "E_datetime" => "2024-04-24 01",
                ],
                "expected_msg" => ["E_datetime" => "E_datetime must be a valid datetime in format Y-m-d H:i"]
            ],
            "Invalid_E_datetime_2" => [
                "data" => [
                    "E_datetime" => "2024-04-24 01:02",
                ],
                "expected_msg" => ["E_datetime" => "E_datetime must be a valid datetime and equal to 2024-04-23 01:01"]
            ],
            "Invalid_F_datetime_1" => [
                "data" => [
                    "F_datetime" => "2024-04-23 01:01",
                ],
                "expected_msg" => ["F_datetime" => "F_datetime must be a valid datetime in format Y-m-d H:i:s"]
            ],
            "Invalid_F_datetime_2" => [
                "data" => [
                    "F_datetime" => "2024-04-23 01:01:02",
                ],
                "expected_msg" => ["F_datetime" => "F_datetime must be a valid datetime and equal to 2024-04-23 01:01:01"]
            ],
            "Invalid_G_datetime_1" => [
                "data" => [
                    "G_datetime" => "2024/04/23 01:61",
                ],
                "expected_msg" => ["G_datetime" => "G_datetime must be a valid datetime in format Y/m/d H:i"]
            ],
            "Invalid_G_datetime_2" => [
                "data" => [
                    "G_datetime" => "2024/04/23 01:02",
                ],
                "expected_msg" => ["G_datetime" => "G_datetime must be a valid datetime and equal to 2024-04-23 01:01"]
            ],
            "Invalid_H_datetime_1" => [
                "data" => [
                    "H_datetime" => "2024/04/23 01:01:60",
                ],
                "expected_msg" => ["H_datetime" => "H_datetime must be a valid datetime in format Y/m/d H:i:s"]
            ],
            "Invalid_H_datetime_2" => [
                "data" => [
                    "H_datetime" => "2024/04/23 01:01:02",
                ],
                "expected_msg" => ["H_datetime" => "H_datetime must be a valid datetime and equal to 2024-04-23 01:01:01"]
            ],
            "Invalid_I_datetime_1" => [
                "data" => [
                    "I_datetime" => '2024-04-23T01:01:01&08:00',
                ],
                "expected_msg" => ["I_datetime" => "I_datetime must be a valid datetime in format RFC3339"]
            ],
            "Invalid_I_datetime_2" => [
                "data" => [
                    "I_datetime" => '2024-04-23T01:01:02+08:00',
                ],
                "expected_msg" => ["I_datetime" => "I_datetime must be a valid datetime and equal to 2024-04-23T01:01:01+08:00"]
            ],
            "Invalid_I_datetime_3" => [
                "data" => [
                    "I_datetime" => '2024-04-23T01:01:01+00:00',
                ],
                "expected_msg" => ["I_datetime" => "I_datetime must be a valid datetime and equal to 2024-04-23T01:01:01+08:00"]
            ],
            "Invalid_J_datetime_1" => [
                "data" => [
                    "J_datetime" => '2024-04-23T01:01:01&08:00',
                ],
                "expected_msg" => ["J_datetime" => "J_datetime must be a valid datetime in format RFC3339"]
            ],
            "Invalid_J_datetime_2" => [
                "data" => [
                    "J_datetime" => '2024-04-23T01:01:02+08:00',
                ],
                "expected_msg" => ["J_datetime" => "J_datetime must be a valid datetime and equal to 2024-04-23 01:01:01"]
            ],
            "Invalid_J_datetime_3" => [
                "data" => [
                    "J_datetime" => '2024-04-23T01:01:01+00:00',
                ],
                "expected_msg" => ["J_datetime" => "J_datetime must be a valid datetime and equal to 2024-04-23 01:01:01"]
            ],
            "Invalid_K_datetime_1" => [
                "data" => [
                    "K_datetime" => 'Tue. 23-Apr-2024 01:01:01 Asia/Shanghai',
                ],
                "expected_msg" => ["K_datetime" => "K_datetime must be a valid datetime in format COOKIE"]
            ],
            "Invalid_K_datetime_2" => [
                "data" => [
                    "K_datetime" => 'Tue, 23-Apr-2024 01:01:02 Asia/Shanghai',
                ],
                "expected_msg" => ["K_datetime" => "K_datetime must be a valid datetime and equal to Tue, 23-Apr-2024 01:01:01 Asia/Shanghai"]
            ],
            "Invalid_K_datetime_3" => [
                "data" => [
                    "K_datetime" => 'Tue, 23-Apr-2024 01:01:01 UTC',
                ],
                "expected_msg" => ["K_datetime" => "K_datetime must be a valid datetime and equal to Tue, 23-Apr-2024 01:01:01 Asia/Shanghai"]
            ],
            "Invalid_L_datetime_1" => [
                "data" => [
                    "L_datetime" => 'Tue. 23-Apr-2024 01:01:01 Asia/Shanghai',
                ],
                "expected_msg" => ["L_datetime" => "L_datetime must be a valid datetime in format COOKIE"]
            ],
            "Invalid_L_datetime_2" => [
                "data" => [
                    "L_datetime" => 'Tue, 23-Apr-2024 01:01:02 Asia/Shanghai',
                ],
                "expected_msg" => ["L_datetime" => "L_datetime must be a valid datetime and equal to 2024-04-23 01:01:01"]
            ],
            "Invalid_L_datetime_3" => [
                "data" => [
                    "L_datetime" => 'Tue, 23-Apr-2024 01:01:01 UTC',
                ],
                "expected_msg" => ["L_datetime" => "L_datetime must be a valid datetime and equal to 2024-04-23 01:01:01"]
            ],
            "Exception_A_exc" => [
                "data" => [
                    "A_exc" => "2024-04-23",
                ],
                "expected_msg" => '@field:A_exc, @method:datetime_equal - Parameter 2024-04-50 is not a valid datetime'
            ],
            "Exception_C_exc" => [
                "data" => [
                    "C_exc" => "23/2024/04",
                ],
                "expected_msg" => '@field:C_exc, @method:datetime_equal - Parameter 2024-13-23 is not a valid datetime'
            ],
            "Exception_F_exc" => [
                "data" => [
                    "F_exc" => "2024-04-23 01:01:01",
                ],
                "expected_msg" => '@field:F_exc, @method:datetime_equal - Parameter 2024-04-23 25:01:01 is not a valid datetime'
            ],
            "Exception_I_exc" => [
                "data" => [
                    "I_exc" => "2024-04-23T01:01:01+08:00",
                ],
                "expected_msg" => '@field:I_exc, @method:datetime_equal - Parameter 2024-04-23T01:0101+08:00 is not a valid datetime'
            ],
            "Exception_K_exc" => [
                "data" => [
                    "K_exc" => "Tue, 23-Apr-2024 01:01:01 Asia/Shanghai",
                ],
                "expected_msg" => '@field:K_exc, @method:datetime_equal - Parameter Tue, 23-Apri-2024 01:01:01 Asia/Shanghai is not a valid datetime'
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
        ];

        return $method_info = [
            "rules" => $rules,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_datetime_not_equal()
    {
        $rules = [
            "symbol" => [
                "A_datetime" => "optional|datetime!=[2024-04-23]",
                "B_datetime" => "optional|datetime!=[2024-04-23 01:01:01,Y-m-d H:i:s]",
                "A_exc" => "optional|datetime!=[2024-04-50]",
            ],
            "method" => [
                "A_datetime" => "optional|datetime_not_equal[2024-04-23]",
                "B_datetime" => "optional|datetime_not_equal[2024-04-23 01:01:01,Y-m-d H:i:s]",
                "A_exc" => "optional|datetime_not_equal[2024-04-50]",
            ]
        ];

        $cases = [
            "Valid_A_datetime_1" => [
                "data" => [
                    "A_datetime" => "2024-04-22",
                ]
            ],
            "Valid_A_datetime_2" => [
                "data" => [
                    "A_datetime" => "2024/04/23 00:00:01",
                ]
            ],
            "Valid_B_datetime_1" => [
                "data" => [
                    "B_datetime" => "2024-04-23 01:01:00",
                ]
            ],
            "Invalid_A_datetime_1" => [
                "data" => [
                    "A_datetime" => "123456789",
                ],
                "expected_msg" => ["A_datetime" => "A_datetime must be a valid datetime"]
            ],
            "Invalid_A_datetime_2" => [
                "data" => [
                    "A_datetime" => "2024-04-23",
                ],
                "expected_msg" => ["A_datetime" => "A_datetime must be a valid datetime and not equal to 2024-04-23"]
            ],
            "Invalid_A_datetime_3" => [
                "data" => [
                    "A_datetime" => "2024-04-23 00:00:00",
                ],
                "expected_msg" => ["A_datetime" => "A_datetime must be a valid datetime and not equal to 2024-04-23"]
            ],
            "Invalid_B_datetime_1" => [
                "data" => [
                    "B_datetime" => "2024-04-23 01:01:01",
                ],
                "expected_msg" => ["B_datetime" => "B_datetime must be a valid datetime and not equal to 2024-04-23 01:01:01"]
            ],
            "Exception_A_exc" => [
                "data" => [
                    "A_exc" => "2024-04-23",
                ],
                "expected_msg" => '@field:A_exc, @method:datetime_not_equal - Parameter 2024-04-50 is not a valid datetime'
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
        ];

        return $method_info = [
            "rules" => $rules,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_datetime_greater_than()
    {
        $rules = [
            "symbol" => [
                "A_datetime" => "optional|datetime>[2024-04-23]",
                "B_datetime" => "optional|datetime>[2024-04-23 12:12:12]",
                "C_datetime" => "optional|datetime>[04/23/2024,m/d/Y]",
            ],
            "method" => [
                "A_datetime" => "optional|datetime_greater_than[2024-04-23]",
                "B_datetime" => "optional|datetime_greater_than[2024-04-23 12:12:12]",
                "C_datetime" => "optional|datetime_greater_than[04/23/2024,m/d/Y]",
            ]
        ];

        $cases = [
            "Valid_A_datetime_1" => [
                "data" => [
                    "A_datetime" => "2024-04-24",
                ]
            ],
            "Valid_A_datetime_2" => [
                "data" => [
                    "A_datetime" => "2024-04-23 00:00:01",
                ]
            ],
            "Valid_B_datetime_1" => [
                "data" => [
                    "B_datetime" => "2024-04-23 12:12:13",
                ]
            ],
            "Valid_B_datetime_2" => [
                "data" => [
                    "B_datetime" => "2024-04-24",
                ]
            ],
            "Valid_C_datetime_1" => [
                "data" => [
                    "C_datetime" => "04/24/2024",
                ]
            ],
            "Invalid_A_datetime_1" => [
                "data" => [
                    "A_datetime" => "2024-04-23",
                ],
                "expected_msg" => ["A_datetime" => "A_datetime must be a valid datetime and greater than 2024-04-23"]
            ],
            "Invalid_B_datetime_1" => [
                "data" => [
                    "B_datetime" => "2024-04-23 12:12:12",
                ],
                "expected_msg" => ["B_datetime" => "B_datetime must be a valid datetime and greater than 2024-04-23 12:12:12"]
            ],
            "Invalid_C_datetime_1" => [
                "data" => [
                    "C_datetime" => "2024-04-23",
                ],
                "expected_msg" => ["C_datetime" => "C_datetime must be a valid datetime in format m/d/Y"]
            ],
            "Invalid_C_datetime_2" => [
                "data" => [
                    "C_datetime" => "04/23/2024",
                ],
                "expected_msg" => ["C_datetime" => "C_datetime must be a valid datetime and greater than 04/23/2024"]
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
        ];

        return $method_info = [
            "rules" => $rules,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_datetime_greater_equal()
    {
        $rules = [
            "symbol" => [
                "A_datetime" => "optional|datetime>=[2024-04-23]",
                "B_datetime" => "optional|datetime>=[2024-04-23 12:12:12]",
                "C_datetime" => "optional|datetime>=[04/23/2024,m/d/Y]",
            ],
            "method" => [
                "A_datetime" => "optional|datetime_greater_equal[2024-04-23]",
                "B_datetime" => "optional|datetime_greater_equal[2024-04-23 12:12:12]",
                "C_datetime" => "optional|datetime_greater_equal[04/23/2024,m/d/Y]",
            ]
        ];

        $cases = [
            "Valid_A_datetime_1" => [
                "data" => [
                    "A_datetime" => "2024-04-23",
                ]
            ],
            "Valid_A_datetime_2" => [
                "data" => [
                    "A_datetime" => "2024-04-24",
                ]
            ],
            "Valid_A_datetime_3" => [
                "data" => [
                    "A_datetime" => "2024-04-23 00:00:00",
                ]
            ],
            "Valid_B_datetime_1" => [
                "data" => [
                    "B_datetime" => "2024-04-23 12:12:13",
                ]
            ],
            "Valid_B_datetime_2" => [
                "data" => [
                    "B_datetime" => "2024-04-23 12:12:13",
                ]
            ],
            "Valid_B_datetime_3" => [
                "data" => [
                    "B_datetime" => "2024-04-24",
                ]
            ],
            "Valid_C_datetime_1" => [
                "data" => [
                    "C_datetime" => "04/23/2024",
                ]
            ],
            "Valid_C_datetime_2" => [
                "data" => [
                    "C_datetime" => "04/24/2024",
                ]
            ],
            "Invalid_A_datetime_1" => [
                "data" => [
                    "A_datetime" => "2024-04-22 23:59:59",
                ],
                "expected_msg" => ["A_datetime" => "A_datetime must be a valid datetime and greater than or equal to 2024-04-23"]
            ],
            "Invalid_B_datetime_1" => [
                "data" => [
                    "B_datetime" => "2024-04-23 12:12:11",
                ],
                "expected_msg" => ["B_datetime" => "B_datetime must be a valid datetime and greater than or equal to 2024-04-23 12:12:12"]
            ],
            "Invalid_C_datetime_1" => [
                "data" => [
                    "C_datetime" => "2024-04-22",
                ],
                "expected_msg" => ["C_datetime" => "C_datetime must be a valid datetime in format m/d/Y"]
            ],
            "Invalid_C_datetime_2" => [
                "data" => [
                    "C_datetime" => "04/22/2024",
                ],
                "expected_msg" => ["C_datetime" => "C_datetime must be a valid datetime and greater than or equal to 04/23/2024"]
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
        ];

        return $method_info = [
            "rules" => $rules,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_datetime_less_than()
    {
        $rules = [
            "symbol" => [
                "A_datetime" => "optional|datetime<[2024-04-23]",
                "B_datetime" => "optional|datetime<[2024-04-23 12:12:12]",
                "C_datetime" => "optional|datetime<[04/23/2024,m/d/Y]",
            ],
            "method" => [
                "A_datetime" => "optional|datetime_less_than[2024-04-23]",
                "B_datetime" => "optional|datetime_less_than[2024-04-23 12:12:12]",
                "C_datetime" => "optional|datetime_less_than[04/23/2024,m/d/Y]",
            ]
        ];

        $cases = [
            "Valid_A_datetime_1" => [
                "data" => [
                    "A_datetime" => "2024-04-22",
                ]
            ],
            "Valid_A_datetime_2" => [
                "data" => [
                    "A_datetime" => "2024-04-22 23:59:59",
                ]
            ],
            "Valid_B_datetime_1" => [
                "data" => [
                    "B_datetime" => "2024-04-23 12:12:11",
                ]
            ],
            "Valid_B_datetime_2" => [
                "data" => [
                    "B_datetime" => "2024-04-23",
                ]
            ],
            "Valid_C_datetime_1" => [
                "data" => [
                    "C_datetime" => "04/22/2024",
                ]
            ],
            "Invalid_A_datetime_1" => [
                "data" => [
                    "A_datetime" => "2024-04-24",
                ],
                "expected_msg" => ["A_datetime" => "A_datetime must be a valid datetime and less than 2024-04-23"]
            ],
            "Invalid_A_datetime_2" => [
                "data" => [
                    "A_datetime" => "2024-04-23 00:00:01",
                ],
                "expected_msg" => ["A_datetime" => "A_datetime must be a valid datetime and less than 2024-04-23"]
            ],
            "Invalid_B_datetime_1" => [
                "data" => [
                    "B_datetime" => "2024-04-23 12:12:12",
                ],
                "expected_msg" => ["B_datetime" => "B_datetime must be a valid datetime and less than 2024-04-23 12:12:12"]
            ],
            "Invalid_B_datetime_2" => [
                "data" => [
                    "B_datetime" => "2024-04-23 12:12:13",
                ],
                "expected_msg" => ["B_datetime" => "B_datetime must be a valid datetime and less than 2024-04-23 12:12:12"]
            ],
            "Invalid_C_datetime_1" => [
                "data" => [
                    "C_datetime" => "2024-04-23",
                ],
                "expected_msg" => ["C_datetime" => "C_datetime must be a valid datetime in format m/d/Y"]
            ],
            "Invalid_C_datetime_2" => [
                "data" => [
                    "C_datetime" => "04/23/2024",
                ],
                "expected_msg" => ["C_datetime" => "C_datetime must be a valid datetime and less than 04/23/2024"]
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
        ];

        return $method_info = [
            "rules" => $rules,
            "cases" => $cases,
            "extra" => $extra
        ];

        $extra = [
            "method_name" => __METHOD__,
        ];

        return $method_info = [
            "rules" => $rules,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_datetime_less_equal()
    {
        $rules = [
            "symbol" => [
                "A_datetime" => "optional|datetime<=[2024-04-23]",
                "B_datetime" => "optional|datetime<=[2024-04-23 12:12:12]",
                "C_datetime" => "optional|datetime<=[04/23/2024,m/d/Y]",
            ],
            "method" => [
                "A_datetime" => "optional|datetime_less_equal[2024-04-23]",
                "B_datetime" => "optional|datetime_less_equal[2024-04-23 12:12:12]",
                "C_datetime" => "optional|datetime_less_equal[04/23/2024,m/d/Y]",
            ]
        ];

        $cases = [
            "Valid_A_datetime_1" => [
                "data" => [
                    "A_datetime" => "2024-04-23",
                ]
            ],
            "Valid_A_datetime_2" => [
                "data" => [
                    "A_datetime" => "2024-04-22",
                ]
            ],
            "Valid_A_datetime_3" => [
                "data" => [
                    "A_datetime" => "2024-04-23 00:00:00",
                ]
            ],
            "Valid_B_datetime_1" => [
                "data" => [
                    "B_datetime" => "2024-04-23 12:12:12",
                ]
            ],
            "Valid_B_datetime_2" => [
                "data" => [
                    "B_datetime" => "2024-04-23 12:12:11",
                ]
            ],
            "Valid_B_datetime_3" => [
                "data" => [
                    "B_datetime" => "2024-04-23",
                ]
            ],
            "Valid_C_datetime_1" => [
                "data" => [
                    "C_datetime" => "04/22/2024",
                ]
            ],
            "Invalid_A_datetime_1" => [
                "data" => [
                    "A_datetime" => "2024-04-24",
                ],
                "expected_msg" => ["A_datetime" => "A_datetime must be a valid datetime and less than or equal to 2024-04-23"]
            ],
            "Invalid_A_datetime_2" => [
                "data" => [
                    "A_datetime" => "2024-04-23 00:00:01",
                ],
                "expected_msg" => ["A_datetime" => "A_datetime must be a valid datetime and less than or equal to 2024-04-23"]
            ],
            "Invalid_B_datetime_1" => [
                "data" => [
                    "B_datetime" => "2024-04-23 12:12:13",
                ],
                "expected_msg" => ["B_datetime" => "B_datetime must be a valid datetime and less than or equal to 2024-04-23 12:12:12"]
            ],
            "Invalid_B_datetime_2" => [
                "data" => [
                    "B_datetime" => "2024-04-24",
                ],
                "expected_msg" => ["B_datetime" => "B_datetime must be a valid datetime and less than or equal to 2024-04-23 12:12:12"]
            ],
            "Invalid_C_datetime_1" => [
                "data" => [
                    "C_datetime" => "2024-04-23",
                ],
                "expected_msg" => ["C_datetime" => "C_datetime must be a valid datetime in format m/d/Y"]
            ],
            "Invalid_C_datetime_2" => [
                "data" => [
                    "C_datetime" => "04/24/2024",
                ],
                "expected_msg" => ["C_datetime" => "C_datetime must be a valid datetime and less than or equal to 04/23/2024"]
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
        ];

        return $method_info = [
            "rules" => $rules,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_datetime_greater_less()
    {
        $rules = [
            "symbol" => [
                "A_datetime" => "optional|datetime><[2024-04-23,2024-05-01]",
                "B_datetime" => "optional|datetime><[2024-04-23 01:01:01,2024-05-01 12:12:12]",
                "C_datetime" => "optional|datetime><[2024-04-23 01:01:01,2024-05-01 12:12:12,m/d/Y H:i:s]",
            ],
            "method" => [
                "A_datetime" => "optional|datetime_greater_less[2024-04-23,2024-05-01]",
                "B_datetime" => "optional|datetime_greater_less[2024-04-23 01:01:01,2024-05-01 12:12:12]",
                "C_datetime" => "optional|datetime_greater_less[2024-04-23 01:01:01,2024-05-01 12:12:12,m/d/Y H:i:s]",
            ]
        ];

        $cases = [
            "Valid_A_datetime_1" => [
                "data" => [
                    "A_datetime" => "2024-04-24",
                ]
            ],
            "Valid_A_datetime_2" => [
                "data" => [
                    "A_datetime" => "2024-04-23 01:01:01",
                ]
            ],
            "Valid_A_datetime_3" => [
                "data" => [
                    "A_datetime" => "2024/04/30 23:59:59",
                ]
            ],
            "Valid_B_datetime_1" => [
                "data" => [
                    "B_datetime" => "2024-04-24",
                ]
            ],
            "Valid_B_datetime_2" => [
                "data" => [
                    "B_datetime" => "2024-04-23 01:01:02",
                ]
            ],
            "Valid_B_datetime_3" => [
                "data" => [
                    "B_datetime" => "2024/05/01 12:12:11",
                ]
            ],
            "Valid_C_datetime_1" => [
                "data" => [
                    "C_datetime" => "04/23/2024 01:01:02",
                ]
            ],
            "Valid_C_datetime_2" => [
                "data" => [
                    "C_datetime" => "05/01/2024 12:12:11",
                ]
            ],
            "Invalid_A_datetime_1" => [
                "data" => [
                    "A_datetime" => "2024-04-23",
                ],
                "expected_msg" => ["A_datetime" => "A_datetime must be a valid datetime and greater than 2024-04-23 and less than 2024-05-01"]
            ],
            "Invalid_A_datetime_2" => [
                "data" => [
                    "A_datetime" => "2024/05/01",
                ],
                "expected_msg" => ["A_datetime" => "A_datetime must be a valid datetime and greater than 2024-04-23 and less than 2024-05-01"]
            ],
            "Invalid_B_datetime_1" => [
                "data" => [
                    "B_datetime" => "2024-04-23 01:01:01",
                ],
                "expected_msg" => ["B_datetime" => "B_datetime must be a valid datetime and greater than 2024-04-23 01:01:01 and less than 2024-05-01 12:12:12"]
            ],
            "Invalid_B_datetime_2" => [
                "data" => [
                    "B_datetime" => "2024-05-01 12:12:12",
                ],
                "expected_msg" => ["B_datetime" => "B_datetime must be a valid datetime and greater than 2024-04-23 01:01:01 and less than 2024-05-01 12:12:12"]
            ],
            "Invalid_B_datetime_3" => [
                "data" => [
                    "B_datetime" => "2024/05/01 12:12:12",
                ],
                "expected_msg" => ["B_datetime" => "B_datetime must be a valid datetime and greater than 2024-04-23 01:01:01 and less than 2024-05-01 12:12:12"]
            ],
            "Invalid_C_datetime_1" => [
                "data" => [
                    "C_datetime" => "2024-04-23 01:01:01",
                ],
                "expected_msg" => ["C_datetime" => "C_datetime must be a valid datetime in format m/d/Y H:i:s"]
            ],
            "Invalid_C_datetime_2" => [
                "data" => [
                    "C_datetime" => "04/23/2024 01:01:01",
                ],
                "expected_msg" => ["C_datetime" => "C_datetime must be a valid datetime and greater than 2024-04-23 01:01:01 and less than 2024-05-01 12:12:12"]
            ],
            "Invalid_C_datetime_3" => [
                "data" => [
                    "C_datetime" => "05/01/2024 12:12:12",
                ],
                "expected_msg" => ["C_datetime" => "C_datetime must be a valid datetime and greater than 2024-04-23 01:01:01 and less than 2024-05-01 12:12:12"]
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
        ];

        return $method_info = [
            "rules" => $rules,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_datetime_greater_lessequal()
    {
        $rules = [
            "symbol" => [
                "A_datetime" => "optional|datetime><=[2024-04-23,2024-05-01]",
                "B_datetime" => "optional|datetime><=[2024-04-23 01:01:01,2024-05-01 12:12:12]",
                "C_datetime" => "optional|datetime><=[2024-04-23 01:01:01,2024-05-01 12:12:12,m/d/Y H:i:s]",
            ],
            "method" => [
                "A_datetime" => "optional|datetime_greater_lessequal[2024-04-23,2024-05-01]",
                "B_datetime" => "optional|datetime_greater_lessequal[2024-04-23 01:01:01,2024-05-01 12:12:12]",
                "C_datetime" => "optional|datetime_greater_lessequal[2024-04-23 01:01:01,2024-05-01 12:12:12,m/d/Y H:i:s]",
            ]
        ];

        $cases = [
            "Valid_A_datetime_1" => [
                "data" => [
                    "A_datetime" => "2024-04-24",
                ]
            ],
            "Valid_A_datetime_2" => [
                "data" => [
                    "A_datetime" => "2024-04-23 01:01:01",
                ]
            ],
            "Valid_A_datetime_3" => [
                "data" => [
                    "A_datetime" => "2024/05/01 00:00:00",
                ]
            ],
            "Valid_B_datetime_1" => [
                "data" => [
                    "B_datetime" => "2024-04-24",
                ]
            ],
            "Valid_B_datetime_2" => [
                "data" => [
                    "B_datetime" => "2024-04-23 01:01:02",
                ]
            ],
            "Valid_B_datetime_3" => [
                "data" => [
                    "B_datetime" => "2024/05/01 12:12:12",
                ]
            ],
            "Valid_C_datetime_1" => [
                "data" => [
                    "C_datetime" => "04/23/2024 01:01:02",
                ]
            ],
            "Valid_C_datetime_2" => [
                "data" => [
                    "C_datetime" => "05/01/2024 12:12:12",
                ]
            ],
            "Invalid_A_datetime_1" => [
                "data" => [
                    "A_datetime" => "2024-04-23",
                ],
                "expected_msg" => ["A_datetime" => "A_datetime must be a valid datetime and greater than 2024-04-23 and less than or equal to 2024-05-01"]
            ],
            "Invalid_A_datetime_2" => [
                "data" => [
                    "A_datetime" => "2024/05/01 00:00:01",
                ],
                "expected_msg" => ["A_datetime" => "A_datetime must be a valid datetime and greater than 2024-04-23 and less than or equal to 2024-05-01"]
            ],
            "Invalid_B_datetime_1" => [
                "data" => [
                    "B_datetime" => "2024-04-23 01:01:01",
                ],
                "expected_msg" => ["B_datetime" => "B_datetime must be a valid datetime and greater than 2024-04-23 01:01:01 and less than or equal to 2024-05-01 12:12:12"]
            ],
            "Invalid_B_datetime_2" => [
                "data" => [
                    "B_datetime" => "2024-05-01 12:12:13",
                ],
                "expected_msg" => ["B_datetime" => "B_datetime must be a valid datetime and greater than 2024-04-23 01:01:01 and less than or equal to 2024-05-01 12:12:12"]
            ],
            "Invalid_B_datetime_3" => [
                "data" => [
                    "B_datetime" => "2024/05/01 12:12:13",
                ],
                "expected_msg" => ["B_datetime" => "B_datetime must be a valid datetime and greater than 2024-04-23 01:01:01 and less than or equal to 2024-05-01 12:12:12"]
            ],
            "Invalid_C_datetime_1" => [
                "data" => [
                    "C_datetime" => "2024-04-23 01:01:01",
                ],
                "expected_msg" => ["C_datetime" => "C_datetime must be a valid datetime in format m/d/Y H:i:s"]
            ],
            "Invalid_C_datetime_2" => [
                "data" => [
                    "C_datetime" => "04/23/2024 01:01:01",
                ],
                "expected_msg" => ["C_datetime" => "C_datetime must be a valid datetime and greater than 2024-04-23 01:01:01 and less than or equal to 2024-05-01 12:12:12"]
            ],
            "Invalid_C_datetime_3" => [
                "data" => [
                    "C_datetime" => "05/01/2024 12:12:13",
                ],
                "expected_msg" => ["C_datetime" => "C_datetime must be a valid datetime and greater than 2024-04-23 01:01:01 and less than or equal to 2024-05-01 12:12:12"]
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
        ];

        return $method_info = [
            "rules" => $rules,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_datetime_greaterequal_less()
    {
        $rules = [
            "symbol" => [
                "A_datetime" => "optional|datetime>=<[2024-04-23,2024-05-01]",
                "B_datetime" => "optional|datetime>=<[2024-04-23 01:01:01,2024-05-01 12:12:12]",
                "C_datetime" => "optional|datetime>=<[2024-04-23 01:01:01,2024-05-01 12:12:12,m/d/Y H:i:s]",
            ],
            "method" => [
                "A_datetime" => "optional|datetime_greaterequal_less[2024-04-23,2024-05-01]",
                "B_datetime" => "optional|datetime_greaterequal_less[2024-04-23 01:01:01,2024-05-01 12:12:12]",
                "C_datetime" => "optional|datetime_greaterequal_less[2024-04-23 01:01:01,2024-05-01 12:12:12,m/d/Y H:i:s]",
            ]
        ];

        $cases = [
            "Valid_A_datetime_1" => [
                "data" => [
                    "A_datetime" => "2024-04-23",
                ]
            ],
            "Valid_A_datetime_2" => [
                "data" => [
                    "A_datetime" => "2024-04-23 00:00:00",
                ]
            ],
            "Valid_A_datetime_3" => [
                "data" => [
                    "A_datetime" => "2024/04/30 23:59:59",
                ]
            ],
            "Valid_B_datetime_1" => [
                "data" => [
                    "B_datetime" => "2024-04-24",
                ]
            ],
            "Valid_B_datetime_2" => [
                "data" => [
                    "B_datetime" => "2024-04-23 01:01:01",
                ]
            ],
            "Valid_B_datetime_3" => [
                "data" => [
                    "B_datetime" => "2024/05/01 12:12:11",
                ]
            ],
            "Valid_C_datetime_1" => [
                "data" => [
                    "C_datetime" => "04/23/2024 01:01:01",
                ]
            ],
            "Valid_C_datetime_2" => [
                "data" => [
                    "C_datetime" => "05/01/2024 12:12:11",
                ]
            ],
            "Invalid_A_datetime_1" => [
                "data" => [
                    "A_datetime" => "2024-04-22",
                ],
                "expected_msg" => ["A_datetime" => "A_datetime must be a valid datetime and greater than or equal to 2024-04-23 and less than 2024-05-01"]
            ],
            "Invalid_A_datetime_2" => [
                "data" => [
                    "A_datetime" => "2024/05/01",
                ],
                "expected_msg" => ["A_datetime" => "A_datetime must be a valid datetime and greater than or equal to 2024-04-23 and less than 2024-05-01"]
            ],
            "Invalid_B_datetime_1" => [
                "data" => [
                    "B_datetime" => "2024-04-23 01:01:00",
                ],
                "expected_msg" => ["B_datetime" => "B_datetime must be a valid datetime and greater than or equal to 2024-04-23 01:01:01 and less than 2024-05-01 12:12:12"]
            ],
            "Invalid_B_datetime_2" => [
                "data" => [
                    "B_datetime" => "2024-05-01 12:12:12",
                ],
                "expected_msg" => ["B_datetime" => "B_datetime must be a valid datetime and greater than or equal to 2024-04-23 01:01:01 and less than 2024-05-01 12:12:12"]
            ],
            "Invalid_B_datetime_3" => [
                "data" => [
                    "B_datetime" => "2024/05/01 12:12:12",
                ],
                "expected_msg" => ["B_datetime" => "B_datetime must be a valid datetime and greater than or equal to 2024-04-23 01:01:01 and less than 2024-05-01 12:12:12"]
            ],
            "Invalid_C_datetime_1" => [
                "data" => [
                    "C_datetime" => "2024-04-23 01:01:01",
                ],
                "expected_msg" => ["C_datetime" => "C_datetime must be a valid datetime in format m/d/Y H:i:s"]
            ],
            "Invalid_C_datetime_2" => [
                "data" => [
                    "C_datetime" => "04/23/2024 01:01:00",
                ],
                "expected_msg" => ["C_datetime" => "C_datetime must be a valid datetime and greater than or equal to 2024-04-23 01:01:01 and less than 2024-05-01 12:12:12"]
            ],
            "Invalid_C_datetime_3" => [
                "data" => [
                    "C_datetime" => "05/01/2024 12:12:12",
                ],
                "expected_msg" => ["C_datetime" => "C_datetime must be a valid datetime and greater than or equal to 2024-04-23 01:01:01 and less than 2024-05-01 12:12:12"]
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
        ];

        return $method_info = [
            "rules" => $rules,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_datetime_between()
    {
        $rules = [
            "symbol" => [
                "A_datetime" => "optional|datetime>=<=[2024-04-23, 2024-05-01]",
                "B_datetime" => "optional|datetime>=<=[2024-04-23, 2024-05-01, Y-m-d]",
                "C_datetime" => "optional|datetime>=<=[2024-04-23, 2024-05-01, m/d/Y]",
                "D_datetime" => "optional|datetime>=<=[2024-04-23 01, 2024-05-01 12, Y-m-d H]",
                "E_datetime" => "optional|datetime>=<=[2024-04-23 01:01, 2024-05-01 12:12, Y-m-d H:i]",
                "F_datetime" => "optional|datetime>=<=[2024-04-23 01:01:01, 2024-05-01 12:12:12, Y-m-d H:i:s]",

                "I_datetime" => "optional|datetime>=<=[2024-04-23T01:01:01+08:00, 2024-05-01T12:12:12+08:00, RFC3339]",
                "J_datetime" => "optional|datetime>=<=[2024-04-23 01:01:01, 2024-05-01 12:12:12, RFC3339]",   // The default timezone is 'Asia/Shanghai', {@see tests/Test.php}
                "K_datetime" => "optional|datetime>=<=[Tue\, 23 Apr 2024 01:01:01 +0800, Wed\, 01 May 2024 12:12:12 +0800, RFC2822]",
                "L_datetime" => "optional|datetime>=<=[2024-04-23 01:01:01, 2024-05-01 12:12:12, RFC2822]",   // The default timezone is 'Asia/Shanghai', {@see tests/Test.php}
                "A1_exc" => "optional|datetime>=<=[2024-04-50, 2024-05-01]",
                "A2_exc" => "optional|datetime>=<=[2024-04-23, 2024-13-01]",
                "E1_exc" => "optional|datetime>=<=[2024-04-23 01:60, 2024-05-01 12:12, Y-m-d H:i]",
                "E2_exc" => "optional|datetime>=<=[2024-04-23 01:01, 2024-05-50 12:12, Y-m-d H:i]",
                "K1_exc" => "optional|datetime>=<=[Tue\, 23 Apri 2024 01:01:01 +0800, Wed\, 01 May 2024 12:12:12 +0800, RFC2822]",
                "K2_exc" => "optional|datetime>=<=[Tue\, 23 Apr 2024 01:01:01 +0800, Wen\, 01 May 2024 12:12:12 +0800, RFC2822]",
            ],
            "method" => [
                "A_datetime" => "optional|datetime_between[2024-04-23, 2024-05-01]",
                "B_datetime" => "optional|datetime_between[2024-04-23, 2024-05-01, Y-m-d]",
                "C_datetime" => "optional|datetime_between[2024-04-23, 2024-05-01, m/d/Y]",
                "D_datetime" => "optional|datetime_between[2024-04-23 01, 2024-05-01 12, Y-m-d H]",
                "E_datetime" => "optional|datetime_between[2024-04-23 01:01, 2024-05-01 12:12, Y-m-d H:i]",
                "F_datetime" => "optional|datetime_between[2024-04-23 01:01:01, 2024-05-01 12:12:12, Y-m-d H:i:s]",

                "I_datetime" => "optional|datetime_between[2024-04-23T01:01:01+08:00, 2024-05-01T12:12:12+08:00, RFC3339]",
                "J_datetime" => "optional|datetime_between[2024-04-23 01:01:01, 2024-05-01 12:12:12, RFC3339]",   // The default timezone is 'Asia/Shanghai', {@see tests/Test.php}
                "K_datetime" => "optional|datetime_between[Tue\, 23 Apr 2024 01:01:01 +0800, Wed\, 01 May 2024 12:12:12 +0800, RFC2822]",
                "L_datetime" => "optional|datetime_between[2024-04-23 01:01:01, 2024-05-01 12:12:12, RFC2822]",   // The default timezone is 'Asia/Shanghai', {@see tests/Test.php}
                "A1_exc" => "optional|datetime_between[2024-04-50, 2024-05-01]",
                "A2_exc" => "optional|datetime_between[2024-04-23, 2024-13-01]",
                "E1_exc" => "optional|datetime_between[2024-04-23 01:60, 2024-05-01 12:12, Y-m-d H:i]",
                "E2_exc" => "optional|datetime_between[2024-04-23 01:01, 2024-05-50 12:12, Y-m-d H:i]",
                "K1_exc" => "optional|datetime_between[Tue\, 23 Apri 2024 01:01:01 +0800, Wed\, 01 May 2024 12:12:12 +0800, RFC2822]",
                "K2_exc" => "optional|datetime_between[Tue\, 23 Apr 2024 01:01:01 +0800, Wen\, 01 May 2024 12:12:12 +0800, RFC2822]",
            ],
        ];

        $cases = [
            "Valid_A_datetime_1" => [
                "data" => [
                    "A_datetime" => "20240424",
                ]
            ],
            "Valid_A_datetime_2" => [
                "data" => [
                    "A_datetime" => '2024-04-24',
                ]
            ],
            "Valid_A_datetime_3" => [
                "data" => [
                    "A_datetime" => '04/24/2024',
                ]
            ],
            "Valid_A_datetime_4" => [
                "data" => [
                    "A_datetime" => '2024-04-23 00:00',
                ]
            ],
            "Valid_A_datetime_5" => [
                "data" => [
                    "A_datetime" => '2024/04/23 00:00:00',
                ]
            ],
            "Valid_A_datetime_6" => [
                "data" => [
                    "A_datetime" => '2024-05-01 00:00:00',
                ]
            ],
            "Valid_B_datetime_1" => [
                "data" => [
                    "B_datetime" => '2024-04-23',
                ]
            ],
            "Valid_B_datetime_2" => [
                "data" => [
                    "B_datetime" => '2024-05-01',
                ]
            ],
            "Valid_B_datetime_3" => [
                "data" => [
                    "B_datetime" => '2024-04-25',
                ]
            ],
            "Valid_C_datetime_1" => [
                "data" => [
                    "C_datetime" => '04/23/2024',
                ]
            ],
            "Valid_C_datetime_2" => [
                "data" => [
                    "C_datetime" => '05/01/2024',
                ]
            ],
            "Valid_C_datetime_3" => [
                "data" => [
                    "C_datetime" => '04/25/2024',
                ]
            ],
            "Valid_D_datetime_1" => [
                "data" => [
                    "D_datetime" => '2024-04-23 01',
                ]
            ],
            "Valid_D_datetime_2" => [
                "data" => [
                    "D_datetime" => '2024-05-01 12',
                ]
            ],
            "Valid_D_datetime_3" => [
                "data" => [
                    "D_datetime" => '2024-05-01 08',
                ]
            ],
            "Valid_E_datetime_1" => [
                "data" => [
                    "E_datetime" => '2024-04-23 01:01',
                ]
            ],
            "Valid_E_datetime_2" => [
                "data" => [
                    "E_datetime" => '2024-05-01 12:12',
                ]
            ],
            "Valid_E_datetime_3" => [
                "data" => [
                    "E_datetime" => '2024-05-01 12:08',
                ]
            ],
            "Valid_F_datetime_1" => [
                "data" => [
                    "F_datetime" => '2024-04-23 01:01:01',
                ]
            ],
            "Valid_F_datetime_2" => [
                "data" => [
                    "F_datetime" => '2024-05-01 12:12:12',
                ]
            ],
            "Valid_F_datetime_3" => [
                "data" => [
                    "F_datetime" => '2024-05-01 12:12:08',
                ]
            ],
            "Valid_I_datetime_1" => [
                "data" => [
                    "I_datetime" => '2024-04-23T01:01:01+08:00',
                ]
            ],
            "Valid_I_datetime_2" => [
                "data" => [
                    "I_datetime" => '2024-05-01T12:12:12+08:00',
                ]
            ],
            "Valid_I_datetime_3" => [
                "data" => [
                    "I_datetime" => '2024-05-01T12:12:08+08:00',
                ]
            ],
            "Valid_I_datetime_4" => [
                "data" => [
                    "I_datetime" => '2024-04-22T17:01:01+00:00',
                ]
            ],
            "Valid_I_datetime_5" => [
                "data" => [
                    "I_datetime" => '2024-05-01T14:12:12+10:00',
                ]
            ],
            "Valid_J_datetime_1" => [
                "data" => [
                    "J_datetime" => '2024-04-23T01:01:01+08:00',
                ]
            ],
            "Valid_J_datetime_2" => [
                "data" => [
                    "J_datetime" => '2024-05-01T12:12:12+08:00',
                ]
            ],
            "Valid_J_datetime_3" => [
                "data" => [
                    "J_datetime" => '2024-05-01T12:12:08+08:00',
                ]
            ],
            "Valid_J_datetime_4" => [
                "data" => [
                    "J_datetime" => '2024-04-22T17:01:01+00:00',
                ]
            ],
            "Valid_J_datetime_5" => [
                "data" => [
                    "J_datetime" => '2024-05-01T14:12:12+10:00',
                ]
            ],
            "Valid_K_datetime_1" => [
                "data" => [
                    "K_datetime" => 'Tue, 23 Apr 2024 01:01:01 +0800',
                ]
            ],
            "Valid_K_datetime_2" => [
                "data" => [
                    "K_datetime" => 'Wed, 01 May 2024 12:12:12 +0800',
                ]
            ],
            "Valid_K_datetime_3" => [
                "data" => [
                    "K_datetime" => 'Wed, 01 May 2024 12:12:08 +0800',
                ]
            ],
            "Valid_K_datetime_4" => [
                "data" => [
                    "K_datetime" => 'Mon, 22 Apr 2024 17:01:01 +0000',
                ]
            ],
            "Valid_K_datetime_5" => [
                "data" => [
                    "K_datetime" => 'Wed, 01 May 2024 16:12:12 +1200',
                ]
            ],
            "Valid_L_datetime_1" => [
                "data" => [
                    "L_datetime" => 'Tue, 23 Apr 2024 01:01:01 +0800',
                ]
            ],
            "Valid_L_datetime_2" => [
                "data" => [
                    "L_datetime" => 'Wed, 01 May 2024 12:12:12 +0800',
                ]
            ],
            "Valid_L_datetime_3" => [
                "data" => [
                    "L_datetime" => 'Wed, 01 May 2024 12:12:08 +0800',
                ]
            ],
            "Valid_L_datetime_4" => [
                "data" => [
                    "L_datetime" => 'Mon, 22 Apr 2024 17:01:01 +0000',
                ]
            ],
            "Valid_L_datetime_5" => [
                "data" => [
                    "L_datetime" => 'Wed, 01 May 2024 16:12:12 +1200',
                ]
            ],

            /**
             * Invalid cases
             */
            "Invalid_A_datetime_1" => [
                "data" => [
                    "A_datetime" => "12345678",
                ],
                "expected_msg" => ["A_datetime" => "A_datetime must be a valid datetime"]
            ],
            "Invalid_A_datetime_2" => [
                "data" => [
                    "A_datetime" => "2024-04-50",
                ],
                "expected_msg" => ["A_datetime" => "A_datetime must be a valid datetime"]
            ],
            "Invalid_A_datetime_3" => [
                "data" => [
                    "A_datetime" => "2024-04-22",
                ],
                "expected_msg" => ["A_datetime" => "A_datetime datetime must be between 2024-04-23 and 2024-05-01"]
            ],
            "Invalid_A_datetime_4" => [
                "data" => [
                    "A_datetime" => "2024-05-02",
                ],
                "expected_msg" => ["A_datetime" => "A_datetime datetime must be between 2024-04-23 and 2024-05-01"]
            ],
            "Invalid_A_datetime_5" => [
                "data" => [
                    "A_datetime" => "2024-05-01 00:00:01",
                ],
                "expected_msg" => ["A_datetime" => "A_datetime datetime must be between 2024-04-23 and 2024-05-01"]
            ],
            "Invalid_B_datetime_1" => [
                "data" => [
                    "B_datetime" => "2024/04/23",
                ],
                "expected_msg" => ["B_datetime" => "B_datetime must be a valid datetime in format Y-m-d"]
            ],
            "Invalid_B_datetime_2" => [
                "data" => [
                    "B_datetime" => "2024-04-50",
                ],
                "expected_msg" => ["B_datetime" => "B_datetime must be a valid datetime in format Y-m-d"]
            ],
            "Invalid_B_datetime_3" => [
                "data" => [
                    "B_datetime" => "2024-04-22",
                ],
                "expected_msg" => ["B_datetime" => "B_datetime datetime must be between 2024-04-23 and 2024-05-01"]
            ],
            "Invalid_B_datetime_4" => [
                "data" => [
                    "B_datetime" => "2024-05-02",
                ],
                "expected_msg" => ["B_datetime" => "B_datetime datetime must be between 2024-04-23 and 2024-05-01"]
            ],
            "Invalid_C_datetime_1" => [
                "data" => [
                    "C_datetime" => "2024/04/23",
                ],
                "expected_msg" => ["C_datetime" => "C_datetime must be a valid datetime in format m/d/Y"]
            ],
            "Invalid_C_datetime_2" => [
                "data" => [
                    "C_datetime" => "04/50/2024",
                ],
                "expected_msg" => ["C_datetime" => "C_datetime must be a valid datetime in format m/d/Y"]
            ],
            "Invalid_C_datetime_3" => [
                "data" => [
                    "C_datetime" => "04/22/2024",
                ],
                "expected_msg" => ["C_datetime" => "C_datetime datetime must be between 2024-04-23 and 2024-05-01"]
            ],
            "Invalid_C_datetime_4" => [
                "data" => [
                    "C_datetime" => "05/02/2024",
                ],
                "expected_msg" => ["C_datetime" => "C_datetime datetime must be between 2024-04-23 and 2024-05-01"]
            ],
            "Invalid_D_datetime_1" => [
                "data" => [
                    "D_datetime" => "2024/04/23 01",
                ],
                "expected_msg" => ["D_datetime" => "D_datetime must be a valid datetime in format Y-m-d H"]
            ],
            "Invalid_D_datetime_2" => [
                "data" => [
                    "D_datetime" => "2024-04-23 24",
                ],
                "expected_msg" => ["D_datetime" => "D_datetime must be a valid datetime in format Y-m-d H"]
            ],
            "Invalid_D_datetime_3" => [
                "data" => [
                    "D_datetime" => "2024-04-23 00",
                ],
                "expected_msg" => ["D_datetime" => "D_datetime datetime must be between 2024-04-23 01 and 2024-05-01 12"]
            ],
            "Invalid_D_datetime_4" => [
                "data" => [
                    "D_datetime" => "2024-05-01 13",
                ],
                "expected_msg" => ["D_datetime" => "D_datetime datetime must be between 2024-04-23 01 and 2024-05-01 12"]
            ],
            "Invalid_E_datetime_1" => [
                "data" => [
                    "E_datetime" => "2024-04-23 01_01",
                ],
                "expected_msg" => ["E_datetime" => "E_datetime must be a valid datetime in format Y-m-d H:i"]
            ],
            "Invalid_E_datetime_2" => [
                "data" => [
                    "E_datetime" => "2024-04-23 01:60",
                ],
                "expected_msg" => ["E_datetime" => "E_datetime must be a valid datetime in format Y-m-d H:i"]
            ],
            "Invalid_E_datetime_3" => [
                "data" => [
                    "E_datetime" => "2024-04-23 01:00",
                ],
                "expected_msg" => ["E_datetime" => "E_datetime datetime must be between 2024-04-23 01:01 and 2024-05-01 12:12"]
            ],
            "Invalid_E_datetime_4" => [
                "data" => [
                    "E_datetime" => "2024-05-01 12:13",
                ],
                "expected_msg" => ["E_datetime" => "E_datetime datetime must be between 2024-04-23 01:01 and 2024-05-01 12:12"]
            ],
            "Invalid_F_datetime_1" => [
                "data" => [
                    "F_datetime" => "2024-04-23 01_01_01",
                ],
                "expected_msg" => ["F_datetime" => "F_datetime must be a valid datetime in format Y-m-d H:i:s"]
            ],
            "Invalid_F_datetime_2" => [
                "data" => [
                    "F_datetime" => "2024-04-23 01:01:60",
                ],
                "expected_msg" => ["F_datetime" => "F_datetime must be a valid datetime in format Y-m-d H:i:s"]
            ],
            "Invalid_F_datetime_3" => [
                "data" => [
                    "F_datetime" => "2024-04-23 01:01:00",
                ],
                "expected_msg" => ["F_datetime" => "F_datetime datetime must be between 2024-04-23 01:01:01 and 2024-05-01 12:12:12"]
            ],
            "Invalid_F_datetime_4" => [
                "data" => [
                    "F_datetime" => "2024-05-01 12:12:13",
                ],
                "expected_msg" => ["F_datetime" => "F_datetime datetime must be between 2024-04-23 01:01:01 and 2024-05-01 12:12:12"]
            ],
            "Invalid_I_datetime_1" => [
                "data" => [
                    "I_datetime" => "2024-04-23T01:01:01@08:00",
                ],
                "expected_msg" => ["I_datetime" => "I_datetime must be a valid datetime in format RFC3339"]
            ],
            "Invalid_I_datetime_2" => [
                "data" => [
                    "I_datetime" => "2024-04-23T24:01:01+08:00",
                ],
                "expected_msg" => ["I_datetime" => "I_datetime must be a valid datetime in format RFC3339"]
            ],
            "Invalid_I_datetime_3" => [
                "data" => [
                    "I_datetime" => "2024-04-23T01:01:00+08:00",
                ],
                "expected_msg" => ["I_datetime" => "I_datetime datetime must be between 2024-04-23T01:01:01+08:00 and 2024-05-01T12:12:12+08:00"]
            ],
            "Invalid_I_datetime_4" => [
                "data" => [
                    "I_datetime" => "2024-05-01T12:12:13+08:00",
                ],
                "expected_msg" => ["I_datetime" => "I_datetime datetime must be between 2024-04-23T01:01:01+08:00 and 2024-05-01T12:12:12+08:00"]
            ],
            "Invalid_I_datetime_5" => [
                "data" => [
                    "I_datetime" => '2024-04-22T17:01:00+00:00',
                ],
                "expected_msg" => ["I_datetime" => "I_datetime datetime must be between 2024-04-23T01:01:01+08:00 and 2024-05-01T12:12:12+08:00"]
            ],
            "Invalid_I_datetime_6" => [
                "data" => [
                    "I_datetime" => '2024-05-01T14:12:13+10:00',
                ],
                "expected_msg" => ["I_datetime" => "I_datetime datetime must be between 2024-04-23T01:01:01+08:00 and 2024-05-01T12:12:12+08:00"]
            ],
            "Invalid_J_datetime_1" => [
                "data" => [
                    "J_datetime" => "2024-04-23T01:01:01@08:00",
                ],
                "expected_msg" => ["J_datetime" => "J_datetime must be a valid datetime in format RFC3339"]
            ],
            "Invalid_J_datetime_2" => [
                "data" => [
                    "J_datetime" => "2024-04-23T24:01:01+08:00",
                ],
                "expected_msg" => ["J_datetime" => "J_datetime must be a valid datetime in format RFC3339"]
            ],
            "Invalid_J_datetime_3" => [
                "data" => [
                    "J_datetime" => "2024-04-23T01:01:00+08:00",
                ],
                "expected_msg" => ["J_datetime" => "J_datetime datetime must be between 2024-04-23 01:01:01 and 2024-05-01 12:12:12"]
            ],
            "Invalid_J_datetime_4" => [
                "data" => [
                    "J_datetime" => "2024-05-01T12:12:13+08:00",
                ],
                "expected_msg" => ["J_datetime" => "J_datetime datetime must be between 2024-04-23 01:01:01 and 2024-05-01 12:12:12"]
            ],
            "Invalid_J_datetime_5" => [
                "data" => [
                    "J_datetime" => '2024-04-22T17:01:00+00:00',
                ],
                "expected_msg" => ["J_datetime" => "J_datetime datetime must be between 2024-04-23 01:01:01 and 2024-05-01 12:12:12"]
            ],
            "Invalid_J_datetime_6" => [
                "data" => [
                    "J_datetime" => '2024-05-01T14:12:13+10:00',
                ],
                "expected_msg" => ["J_datetime" => "J_datetime datetime must be between 2024-04-23 01:01:01 and 2024-05-01 12:12:12"]
            ],
            "Invalid_K_datetime_1" => [
                "data" => [
                    "K_datetime" => "Tue, 23 Apr 2024 01:01:01 #0800",
                ],
                "expected_msg" => ["K_datetime" => "K_datetime must be a valid datetime in format RFC2822"]
            ],
            "Invalid_K_datetime_2" => [
                "data" => [
                    "K_datetime" => "Tue, 23 Apr 2024 01:60:01 +0800",
                ],
                "expected_msg" => ["K_datetime" => "K_datetime must be a valid datetime in format RFC2822"]
            ],
            "Invalid_K_datetime_3" => [
                "data" => [
                    "K_datetime" => "Tue, 23 Apr 2024 01:01:00 +0800",
                ],
                "expected_msg" => ["K_datetime" => "K_datetime datetime must be between Tue, 23 Apr 2024 01:01:01 +0800 and Wed, 01 May 2024 12:12:12 +0800"]
            ],
            "Invalid_K_datetime_4" => [
                "data" => [
                    "K_datetime" => "Wed, 01 May 2024 12:12:13 +0800",
                ],
                "expected_msg" => ["K_datetime" => "K_datetime datetime must be between Tue, 23 Apr 2024 01:01:01 +0800 and Wed, 01 May 2024 12:12:12 +0800"]
            ],
            "Invalid_K_datetime_5" => [
                "data" => [
                    "K_datetime" => 'Mon, 22 Apr 2024 17:01:00 +0000',
                ],
                "expected_msg" => ["K_datetime" => "K_datetime datetime must be between Tue, 23 Apr 2024 01:01:01 +0800 and Wed, 01 May 2024 12:12:12 +0800"]
            ],
            "Invalid_K_datetime_6" => [
                "data" => [
                    "K_datetime" => 'Wed, 01 May 2024 16:12:13 +1200',
                ],
                "expected_msg" => ["K_datetime" => "K_datetime datetime must be between Tue, 23 Apr 2024 01:01:01 +0800 and Wed, 01 May 2024 12:12:12 +0800"]
            ],
            "Invalid_L_datetime_1" => [
                "data" => [
                    "L_datetime" => "Tue, 23 Apr 2024 01:01:01 #0800",
                ],
                "expected_msg" => ["L_datetime" => "L_datetime must be a valid datetime in format RFC2822"]
            ],
            "Invalid_L_datetime_2" => [
                "data" => [
                    "L_datetime" => "Tue, 23 Apr 2024 01:60:01 +0800",
                ],
                "expected_msg" => ["L_datetime" => "L_datetime must be a valid datetime in format RFC2822"]
            ],
            "Invalid_L_datetime_3" => [
                "data" => [
                    "L_datetime" => "Tue, 23 Apr 2024 01:01:00 +0800",
                ],
                "expected_msg" => ["L_datetime" => "L_datetime datetime must be between 2024-04-23 01:01:01 and 2024-05-01 12:12:12"]
            ],
            "Invalid_L_datetime_4" => [
                "data" => [
                    "L_datetime" => "Wed, 01 May 2024 12:12:13 +0800",
                ],
                "expected_msg" => ["L_datetime" => "L_datetime datetime must be between 2024-04-23 01:01:01 and 2024-05-01 12:12:12"]
            ],
            "Invalid_L_datetime_5" => [
                "data" => [
                    "L_datetime" => 'Mon, 22 Apr 2024 17:01:00 +0000',
                ],
                "expected_msg" => ["L_datetime" => "L_datetime datetime must be between 2024-04-23 01:01:01 and 2024-05-01 12:12:12"]
            ],
            "Invalid_L_datetime_6" => [
                "data" => [
                    "L_datetime" => 'Wed, 01 May 2024 16:12:13 +1200',
                ],
                "expected_msg" => ["L_datetime" => "L_datetime datetime must be between 2024-04-23 01:01:01 and 2024-05-01 12:12:12"]
            ],
            "Exception_A1_exc" => [
                "data" => [
                    "A1_exc" => "2024-05-01",
                ],
                "expected_msg" => '@field:A1_exc, @method:datetime_between - Parameter 2024-04-50 is not a valid datetime'
            ],
            "Exception_A2_exc" => [
                "data" => [
                    "A2_exc" => "2024-05-01",
                ],
                "expected_msg" => '@field:A2_exc, @method:datetime_between - Parameter 2024-13-01 is not a valid datetime'
            ],
            "Exception_E1_exc" => [
                "data" => [
                    "E1_exc" => "2024-05-01 12:12",
                ],
                "expected_msg" => '@field:E1_exc, @method:datetime_between - Parameter 2024-04-23 01:60 is not a valid datetime'
            ],
            "Exception_E2_exc" => [
                "data" => [
                    "E2_exc" => "2024-05-01 12:12",
                ],
                "expected_msg" => '@field:E2_exc, @method:datetime_between - Parameter 2024-05-50 12:12 is not a valid datetime'
            ],
            "Exception_K1_exc" => [
                "data" => [
                    "K1_exc" => "Wed, 01 May 2024 12:12:12 +0800",
                ],
                "expected_msg" => '@field:K1_exc, @method:datetime_between - Parameter Tue, 23 Apri 2024 01:01:01 +0800 is not a valid datetime'
            ],
            "Exception_K2_exc" => [
                "data" => [
                    "K2_exc" => "Wed, 01 May 2024 12:12:12 +0800",
                ],
                "expected_msg" => '@field:K2_exc, @method:datetime_between - Parameter Wen, 01 May 2024 12:12:12 +0800 is not a valid datetime'
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
        ];

        return $method_info = [
            "rules" => $rules,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_date()
    {
        $rules = [
            "symbol" => [
                "A_date" => "optional|date",
                "B_date" => "optional|date[Y-m-d]",
                "C_date" => "optional|date[Y/m/d]",
                "D_date" => "optional|date[Y/m-d]",
                "E_date" => "optional|date[d/m/Y]",
                "F_date" => "optional|date[Y-m]",
                "G_date" => "optional|date[d/m]",
                "H_date" => "optional|date[d]",
                "I_date" => "optional|date[m]",
                "J_date" => "optional|date[Y]",
            ],
            "method" => [
                "A_date" => "optional|is_date",
                "B_date" => "optional|is_date[Y-m-d]",
                "C_date" => "optional|is_date[Y/m/d]",
                "D_date" => "optional|is_date[Y/m-d]",
                "E_date" => "optional|is_date[d/m/Y]",
                "F_date" => "optional|is_date[Y-m]",
                "G_date" => "optional|is_date[d/m]",
                "H_date" => "optional|is_date[d]",
                "I_date" => "optional|is_date[m]",
                "J_date" => "optional|is_date[Y]",
            ],
        ];

        $cases = [
            "Valid_A_date_1" => [
                "data" => [
                    "A_date" => '2024-04-23',
                ]
            ],
            "Valid_B_date_1" => [
                "data" => [
                    "B_date" => '2024-04-23',
                ]
            ],
            "Valid_C_date_1" => [
                "data" => [
                    "C_date" => '2024/04/23',
                ]
            ],
            "Valid_D_date_1" => [
                "data" => [
                    "D_date" => '2024/04-23',
                ]
            ],
            "Valid_E_date_1" => [
                "data" => [
                    "E_date" => '23/04/2024',
                ]
            ],
            "Valid_F_date_1" => [
                "data" => [
                    "F_date" => '2024-04',
                ]
            ],
            "Valid_G_date_1" => [
                "data" => [
                    "G_date" => '23/04',
                ]
            ],
            "Valid_H_date_1" => [
                "data" => [
                    "H_date" => '23',
                ]
            ],
            "Valid_I_date_1" => [
                "data" => [
                    "I_date" => '04',
                ]
            ],
            "Valid_J_date_1" => [
                "data" => [
                    "J_date" => '2024',
                ]
            ],

            /**
             * Invalid cases
             */
            "Invalid_A_date_1" => [
                "data" => [
                    "A_date" => "12345678",
                ],
                "expected_msg" => ["A_date" => "A_date must be a valid date in format Y-m-d"]
            ],
            "Invalid_A_date_2" => [
                "data" => [
                    "A_date" => "23/04/2024",
                ],
                "expected_msg" => ["A_date" => "A_date must be a valid date in format Y-m-d"]
            ],
            "Invalid_A_date_3" => [
                "data" => [
                    "A_date" => "2024-04-50",
                ],
                "expected_msg" => ["A_date" => "A_date must be a valid date in format Y-m-d"]
            ],
            "Invalid_A_date_4" => [
                "data" => [
                    "A_date" => "2024/04/23",
                ],
                "expected_msg" => ["A_date" => "A_date must be a valid date in format Y-m-d"]
            ],
            "Invalid_B_date_1" => [
                "data" => [
                    "B_date" => "2024-04-50",
                ],
                "expected_msg" => ["B_date" => "B_date must be a valid date in format Y-m-d"]
            ],
            "Invalid_B_date_2" => [
                "data" => [
                    "B_date" => "2024/04/23",
                ],
                "expected_msg" => ["B_date" => "B_date must be a valid date in format Y-m-d"]
            ],
            "Invalid_C_date_1" => [
                "data" => [
                    "C_date" => "2024/04/50",
                ],
                "expected_msg" => ["C_date" => "C_date must be a valid date in format Y/m/d"]
            ],
            "Invalid_C_date_2" => [
                "data" => [
                    "C_date" => "2024/04-23",
                ],
                "expected_msg" => ["C_date" => "C_date must be a valid date in format Y/m/d"]
            ],
            "Invalid_D_date_1" => [
                "data" => [
                    "D_date" => "2024/04-50",
                ],
                "expected_msg" => ["D_date" => "D_date must be a valid date in format Y/m-d"]
            ],
            "Invalid_D_date_2" => [
                "data" => [
                    "D_date" => "2024-04-23",
                ],
                "expected_msg" => ["D_date" => "D_date must be a valid date in format Y/m-d"]
            ],
            "Invalid_E_date_1" => [
                "data" => [
                    "E_date" => "50/04/2024",
                ],
                "expected_msg" => ["E_date" => "E_date must be a valid date in format d/m/Y"]
            ],
            "Invalid_E_date_2" => [
                "data" => [
                    "E_date" => "2024-04-23",
                ],
                "expected_msg" => ["E_date" => "E_date must be a valid date in format d/m/Y"]
            ],
            "Invalid_F_date_1" => [
                "data" => [
                    "F_date" => "2024-13",
                ],
                "expected_msg" => ["F_date" => "F_date must be a valid date in format Y-m"]
            ],
            "Invalid_F_date_2" => [
                "data" => [
                    "F_date" => "20240-04",
                ],
                "expected_msg" => ["F_date" => "F_date must be a valid date in format Y-m"]
            ],
            "Invalid_G_date_1" => [
                "data" => [
                    "G_date" => "23/13",
                ],
                "expected_msg" => ["G_date" => "G_date must be a valid date in format d/m"]
            ],
            "Invalid_G_date_2" => [
                "data" => [
                    "G_date" => "32/04",
                ],
                "expected_msg" => ["G_date" => "G_date must be a valid date in format d/m"]
            ],
            "Invalid_H_date_1" => [
                "data" => [
                    "H_date" => "32",
                ],
                "expected_msg" => ["H_date" => "H_date must be a valid date in format d"]
            ],
            "Invalid_H_date_2" => [
                "data" => [
                    "H_date" => "00",
                ],
                "expected_msg" => ["H_date" => "H_date must be a valid date in format d"]
            ],
            "Invalid_I_date_1" => [
                "data" => [
                    "I_date" => "13",
                ],
                "expected_msg" => ["I_date" => "I_date must be a valid date in format m"]
            ],
            "Invalid_I_date_2" => [
                "data" => [
                    "I_date" => "00",
                ],
                "expected_msg" => ["I_date" => "I_date must be a valid date in format m"]
            ],
            "Invalid_J_date_1" => [
                "data" => [
                    "J_date" => "12345",
                ],
                "expected_msg" => ["J_date" => "J_date must be a valid date in format Y"]
            ],
            "Invalid_J_date_2" => [
                "data" => [
                    "J_date" => "01234",
                ],
                "expected_msg" => ["J_date" => "J_date must be a valid date in format Y"]
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
        ];

        return $method_info = [
            "rules" => $rules,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_date_equal()
    {
        $rules = [
            "symbol" => [
                "A_date" => "optional|date=[2024-04-23]",
                "B_date" => "optional|date=[2024-04-23,Y-m-d]",
                "C_date" => "optional|date=[2024-04-23,d/Y/m]",
                "D_date" => "optional|date=[2024-04-23 00:00:00,Y-m-d H:i:s]",      // date method is not recommended using time part H:i:s(00:00:00)
                "E_date" => "optional|date=[2024-04-23T00:00:00+08:00,RFC3339]",    // date method is not recommended using the format contains time part
                "A_exc" => "optional|date=[2024-04-50]",
                "C_exc" => "optional|date=[2024-13-23,d/Y/m]",
            ],
            "method" => [
                "A_date" => "optional|date_equal[2024-04-23]",
                "B_date" => "optional|date_equal[2024-04-23,Y-m-d]",
                "C_date" => "optional|date_equal[2024-04-23,d/Y/m]",
                "D_date" => "optional|date_equal[2024-04-23 00:00:00,Y-m-d H:i:s]",      // date method is not recommended using time part H:i:s(00:00:00)
                "E_date" => "optional|date_equal[2024-04-23T00:00:00+08:00,RFC3339]",    // date method is not recommended using the format contains time part
                "A_exc" => "optional|date_equal[2024-04-50]",
                "C_exc" => "optional|date_equal[2024-13-23,d/Y/m]",
            ]
        ];

        $cases = [
            "Valid_A_date_1" => [
                "data" => [
                    "A_date" => '2024-04-23',
                ]
            ],
            "Valid_B_date_1" => [
                "data" => [
                    "B_date" => '2024-04-23',
                ]
            ],
            "Valid_C_date_1" => [
                "data" => [
                    "C_date" => '23/2024/04',
                ]
            ],
            "Valid_D_date_1" => [
                "data" => [
                    "D_date" => '2024-04-23 00:00:00',
                ]
            ],
            "Valid_E_date_1" => [
                "data" => [
                    "E_date" => '2024-04-23T00:00:00+08:00',
                ]
            ],

            /**
             * Invalid cases
             */
            "Invalid_A_date_1" => [
                "data" => [
                    "A_date" => "12345678",
                ],
                "expected_msg" => ["A_date" => "A_date must be a valid date in format Y-m-d"]
            ],
            "Invalid_A_date_2" => [
                "data" => [
                    "A_date" => "23/04/2024",
                ],
                "expected_msg" => ["A_date" => "A_date must be a valid date in format Y-m-d"]
            ],
            "Invalid_A_date_3" => [
                "data" => [
                    "A_date" => "2024-04-50",
                ],
                "expected_msg" => ["A_date" => "A_date must be a valid date in format Y-m-d"]
            ],
            "Invalid_A_date_4" => [
                "data" => [
                    "A_date" => "2023/04/23",
                ],
                "expected_msg" => ["A_date" => "A_date must be a valid date in format Y-m-d"]
            ],
            "Invalid_A_date_5" => [
                "data" => [
                    "A_date" => "2024-05-01",
                ],
                "expected_msg" => ["A_date" => "A_date must be a valid date and equal to 2024-04-23"]
            ],
            "Invalid_B_date_1" => [
                "data" => [
                    "B_date" => "2024/04/23",
                ],
                "expected_msg" => ["B_date" => "B_date must be a valid date in format Y-m-d"]
            ],
            "Invalid_B_date_2" => [
                "data" => [
                    "B_date" => "2024-04-23 01:00:00",
                ],
                "expected_msg" => ["B_date" => "B_date must be a valid date in format Y-m-d"]
            ],
            "Invalid_B_date_3" => [
                "data" => [
                    "B_date" => "2024-04-24",
                ],
                "expected_msg" => ["B_date" => "B_date must be a valid date and equal to 2024-04-23"]
            ],
            "Invalid_C_date_1" => [
                "data" => [
                    "C_date" => "2024/04/23",
                ],
                "expected_msg" => ["C_date" => "C_date must be a valid date in format d/Y/m"]
            ],
            "Invalid_C_date_2" => [
                "data" => [
                    "C_date" => "2024-04-23 01:00:00",
                ],
                "expected_msg" => ["C_date" => "C_date must be a valid date in format d/Y/m"]
            ],
            "Invalid_C_date_3" => [
                "data" => [
                    "C_date" => "24/2024/04",
                ],
                "expected_msg" => ["C_date" => "C_date must be a valid date and equal to 2024-04-23"]
            ],
            "Invalid_D_date_1" => [
                "data" => [
                    "D_date" => "2024-04-23 01:01:01",
                ],
                "expected_msg" => ["D_date" => 'D_date format Y-m-d H:i:s is not a valid date format']
            ],
            "Invalid_D_date_2" => [
                "data" => [
                    "D_date" => "2024-04-23",
                ],
                "expected_msg" => ["D_date" => 'D_date must be a valid date in format Y-m-d H:i:s']
            ],
            "Invalid_E_date_1" => [
                "data" => [
                    "E_date" => '2024-04-23T00:00:01+08:00',
                ],
                "expected_msg" => ["E_date" => 'E_date format RFC3339 is not a valid date format']
            ],
            "Invalid_E_date_2" => [
                "data" => [
                    "E_date" => '2024-04-23',
                ],
                "expected_msg" => ["E_date" => 'E_date must be a valid date in format RFC3339']
            ],

            /**
             * Exception cases
             */
            "Exception_A_exc" => [
                "data" => [
                    "A_exc" => "2024-04-23",
                ],
                "expected_msg" => '@field:A_exc, @method:date_equal - Parameter 2024-04-50 is not a valid date'
            ],
            "Exception_C_exc" => [
                "data" => [
                    "C_exc" => "23/2024/04",
                ],
                "expected_msg" => '@field:C_exc, @method:date_equal - Parameter 2024-13-23 is not a valid date'
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
        ];

        return $method_info = [
            "rules" => $rules,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_date_not_equal()
    {
        $rules = [
            "symbol" => [
                "A_date" => "optional|date!=[2024-04-23]",
                "A_exc" => "optional|date!=[2024-04-50]",
            ],
            "method" => [
                "A_date" => "optional|date_not_equal[2024-04-23]",
                "A_exc" => "optional|date_not_equal[2024-04-50]",
            ]
        ];

        $cases = [
            "Valid_A_date_1" => [
                "data" => [
                    "A_date" => "2024-04-22",
                ]
            ],
            "Invalid_A_date_1" => [
                "data" => [
                    "A_date" => "123456789",
                ],
                "expected_msg" => ["A_date" => "A_date must be a valid date in format Y-m-d"]
            ],
            "Invalid_A_date_2" => [
                "data" => [
                    "A_date" => "2024-04-23",
                ],
                "expected_msg" => ["A_date" => "A_date must be a valid date and not equal to 2024-04-23"]
            ],
            "Invalid_A_date_3" => [
                "data" => [
                    "A_date" => "2024-04-23 00:00:00",
                ],
                "expected_msg" => ["A_date" => "A_date must be a valid date in format Y-m-d"]
            ],
            "Exception_A_exc" => [
                "data" => [
                    "A_exc" => "2024-04-23",
                ],
                "expected_msg" => '@field:A_exc, @method:date_not_equal - Parameter 2024-04-50 is not a valid date'
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
        ];

        return $method_info = [
            "rules" => $rules,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_date_greater_than()
    {
        $rules = [
            "symbol" => [
                "A_date" => "optional|date>[2024-04-23]",
                "B_date" => "optional|date>[04/23/2024,m/d/Y]",
                "C_date" => "optional|date>[2024-04,Y-m]",
                "D_date" => "optional|date>[04/23,m/d]",
                "E_date" => "optional|date>[23,d]",
            ],
            "method" => [
                "A_date" => "optional|date_greater_than[2024-04-23]",
                "B_date" => "optional|date_greater_than[04/23/2024,m/d/Y]",
                "C_date" => "optional|date>[2024-04,Y-m]",
                "D_date" => "optional|date>[04/23,m/d]",
                "E_date" => "optional|date>[23,d]",
            ]
        ];

        $cases = [
            "Valid_A_date_1" => [
                "data" => [
                    "A_date" => "2024-04-24",
                ]
            ],
            "Valid_B_date_1" => [
                "data" => [
                    "B_date" => "04/24/2024",
                ]
            ],
            "Valid_C_date_1" => [
                "data" => [
                    "C_date" => "2024-05",
                ]
            ],
            "Valid_C_date_2" => [
                "data" => [
                    "C_date" => "2025-01",
                ]
            ],
            "Valid_D_date_1" => [
                "data" => [
                    "D_date" => "04/24",
                ]
            ],
            "Valid_D_date_2" => [
                "data" => [
                    "D_date" => "05/01",
                ]
            ],
            "Valid_E_date_1" => [
                "data" => [
                    "E_date" => "24",
                ]
            ],
            "Valid_E_date_2" => [
                "data" => [
                    "E_date" => "31",
                ]
            ],
            "Invalid_A_date_1" => [
                "data" => [
                    "A_date" => "2024/04/23",
                ],
                "expected_msg" => ["A_date" => "A_date must be a valid date in format Y-m-d"]
            ],
            "Invalid_A_date_2" => [
                "data" => [
                    "A_date" => "2024-04-23",
                ],
                "expected_msg" => ["A_date" => "A_date must be a valid date and greater than 2024-04-23"]
            ],
            "Invalid_B_date_1" => [
                "data" => [
                    "B_date" => "2024-04-23",
                ],
                "expected_msg" => ["B_date" => "B_date must be a valid date in format m/d/Y"]
            ],
            "Invalid_B_date_2" => [
                "data" => [
                    "B_date" => "04/23/2024",
                ],
                "expected_msg" => ["B_date" => "B_date must be a valid date and greater than 04/23/2024"]
            ],
            "Invalid_C_date_1" => [
                "data" => [
                    "C_date" => "2024-13",
                ],
                "expected_msg" => ["C_date" => "C_date must be a valid date in format Y-m"]
            ],
            "Invalid_C_date_2" => [
                "data" => [
                    "C_date" => "2024-04",
                ],
                "expected_msg" => ["C_date" => "C_date must be a valid date and greater than 2024-04"]
            ],
            "Invalid_C_date_3" => [
                "data" => [
                    "C_date" => "2023-12",
                ],
                "expected_msg" => ["C_date" => "C_date must be a valid date and greater than 2024-04"]
            ],
            "Invalid_D_date_1" => [
                "data" => [
                    "D_date" => "13/23",
                ],
                "expected_msg" => ["D_date" => "D_date must be a valid date in format m/d"]
            ],
            "Invalid_D_date_2" => [
                "data" => [
                    "D_date" => "04/23",
                ],
                "expected_msg" => ["D_date" => "D_date must be a valid date and greater than 04/23"]
            ],
            "Invalid_D_date_3" => [
                "data" => [
                    "D_date" => "03/30",
                ],
                "expected_msg" => ["D_date" => "D_date must be a valid date and greater than 04/23"]
            ],
            "Invalid_E_date_1" => [
                "data" => [
                    "E_date" => "32",
                ],
                "expected_msg" => ["E_date" => "E_date must be a valid date in format d"]
            ],
            "Invalid_E_date_2" => [
                "data" => [
                    "E_date" => "23",
                ],
                "expected_msg" => ["E_date" => "E_date must be a valid date and greater than 23"]
            ],
            "Invalid_E_date_3" => [
                "data" => [
                    "E_date" => "01",
                ],
                "expected_msg" => ["E_date" => "E_date must be a valid date and greater than 23"]
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
        ];

        return $method_info = [
            "rules" => $rules,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_date_greater_equal()
    {
        $rules = [
            "symbol" => [
                "A_date" => "optional|date>=[2024-04-23]",
                "B_date" => "optional|date>=[04/23/2024,m/d/Y]",
            ],
            "method" => [
                "A_date" => "optional|date_greater_equal[2024-04-23]",
                "B_date" => "optional|date_greater_equal[04/23/2024,m/d/Y]",
            ]
        ];

        $cases = [
            "Valid_A_date_1" => [
                "data" => [
                    "A_date" => "2024-04-23",
                ]
            ],
            "Valid_A_date_2" => [
                "data" => [
                    "A_date" => "2024-04-24",
                ]
            ],
            "Valid_B_date_1" => [
                "data" => [
                    "B_date" => "04/23/2024",
                ]
            ],
            "Valid_B_date_2" => [
                "data" => [
                    "B_date" => "04/24/2024",
                ]
            ],
            "Invalid_A_date_1" => [
                "data" => [
                    "A_date" => "2024-04-22 23:59:59",
                ],
                "expected_msg" => ["A_date" => "A_date must be a valid date in format Y-m-d"]
            ],
            "Invalid_A_date_2" => [
                "data" => [
                    "A_date" => "2024-04-22",
                ],
                "expected_msg" => ["A_date" => "A_date must be a valid date and greater than or equal to 2024-04-23"]
            ],
            "Invalid_B_date_1" => [
                "data" => [
                    "B_date" => "2024-04-22",
                ],
                "expected_msg" => ["B_date" => "B_date must be a valid date in format m/d/Y"]
            ],
            "Invalid_B_date_2" => [
                "data" => [
                    "B_date" => "04/22/2024",
                ],
                "expected_msg" => ["B_date" => "B_date must be a valid date and greater than or equal to 04/23/2024"]
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
        ];

        return $method_info = [
            "rules" => $rules,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_date_less_than()
    {
        $rules = [
            "symbol" => [
                "A_date" => "optional|date<[2024-04-23]",
                "B_date" => "optional|date<[04/23/2024,m/d/Y]",
            ],
            "method" => [
                "A_date" => "optional|date_less_than[2024-04-23]",
                "B_date" => "optional|date_less_than[04/23/2024,m/d/Y]",
            ]
        ];

        $cases = [
            "Valid_A_date_1" => [
                "data" => [
                    "A_date" => "2024-04-22",
                ]
            ],
            "Valid_A_date_2" => [
                "data" => [
                    "A_date" => "1970-01-01",
                ]
            ],
            "Valid_B_date_1" => [
                "data" => [
                    "B_date" => "04/22/2024",
                ]
            ],
            "Invalid_A_date_1" => [
                "data" => [
                    "A_date" => "2024-04-23 00:00:00",
                ],
                "expected_msg" => ["A_date" => "A_date must be a valid date in format Y-m-d"]
            ],
            "Invalid_A_date_2" => [
                "data" => [
                    "A_date" => "2024-04-24",
                ],
                "expected_msg" => ["A_date" => "A_date must be a valid date and less than 2024-04-23"]
            ],
            "Invalid_B_date_1" => [
                "data" => [
                    "B_date" => "2024-04-23",
                ],
                "expected_msg" => ["B_date" => "B_date must be a valid date in format m/d/Y"]
            ],
            "Invalid_B_date_2" => [
                "data" => [
                    "B_date" => "04/23/2024",
                ],
                "expected_msg" => ["B_date" => "B_date must be a valid date and less than 04/23/2024"]
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
        ];

        return $method_info = [
            "rules" => $rules,
            "cases" => $cases,
            "extra" => $extra
        ];

        $extra = [
            "method_name" => __METHOD__,
        ];

        return $method_info = [
            "rules" => $rules,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_date_less_equal()
    {
        $rules = [
            "symbol" => [
                "A_date" => "optional|date<=[2024-04-23]",
                "B_date" => "optional|date<=[04/23/2024,m/d/Y]",
            ],
            "method" => [
                "A_date" => "optional|date_less_equal[2024-04-23]",
                "B_date" => "optional|date_less_equal[04/23/2024,m/d/Y]",
            ]
        ];

        $cases = [
            "Valid_A_date_1" => [
                "data" => [
                    "A_date" => "2024-04-23",
                ]
            ],
            "Valid_A_date_2" => [
                "data" => [
                    "A_date" => "2024-04-22",
                ]
            ],
            "Valid_B_date_1" => [
                "data" => [
                    "B_date" => "04/22/2024",
                ]
            ],
            "Invalid_A_date_1" => [
                "data" => [
                    "A_date" => "2024-04-23 00:00:01",
                ],
                "expected_msg" => ["A_date" => "A_date must be a valid date in format Y-m-d"]
            ],
            "Invalid_A_date_2" => [
                "data" => [
                    "A_date" => "2024-04-24",
                ],
                "expected_msg" => ["A_date" => "A_date must be a valid date and less than or equal to 2024-04-23"]
            ],
            "Invalid_B_date_1" => [
                "data" => [
                    "B_date" => "2024-04-23",
                ],
                "expected_msg" => ["B_date" => "B_date must be a valid date in format m/d/Y"]
            ],
            "Invalid_B_date_2" => [
                "data" => [
                    "B_date" => "04/24/2024",
                ],
                "expected_msg" => ["B_date" => "B_date must be a valid date and less than or equal to 04/23/2024"]
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
        ];

        return $method_info = [
            "rules" => $rules,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_date_greater_less()
    {
        $rules = [
            "symbol" => [
                "A_date" => "optional|date><[2024-04-23,2024-05-01]",
                "B_date" => "optional|date><[2024-04-23,2024-05-01,m/d/Y]",
                "C_date" => "optional|date><[2024-04-23 01:01:01,2024-05-01 12:12:12,m/d/Y H:i:s]",
            ],
            "method" => [
                "A_date" => "optional|date_greater_less[2024-04-23,2024-05-01]",
                "B_date" => "optional|date_greater_less[2024-04-23,2024-05-01,m/d/Y]",
                "C_date" => "optional|date_greater_less[2024-04-23 01:01:01,2024-05-01 12:12:12,m/d/Y H:i:s]",
            ]
        ];

        $cases = [
            "Valid_A_date_1" => [
                "data" => [
                    "A_date" => "2024-04-24",
                ]
            ],
            "Valid_A_date_2" => [
                "data" => [
                    "A_date" => "2024-04-30",
                ]
            ],
            "Valid_B_date_1" => [
                "data" => [
                    "B_date" => "04/24/2024",
                ]
            ],
            "Valid_B_date_2" => [
                "data" => [
                    "B_date" => "04/30/2024",
                ]
            ],
            "Invalid_A_date_1" => [
                "data" => [
                    "A_date" => "2024-04-23",
                ],
                "expected_msg" => ["A_date" => "A_date must be a valid date and greater than 2024-04-23 and less than 2024-05-01"]
            ],
            "Invalid_A_date_2" => [
                "data" => [
                    "A_date" => "2024-05-01",
                ],
                "expected_msg" => ["A_date" => "A_date must be a valid date and greater than 2024-04-23 and less than 2024-05-01"]
            ],
            "Invalid_A_date_3" => [
                "data" => [
                    "A_date" => "2024-04-24 00:00:00",
                ],
                "expected_msg" => ["A_date" => "A_date must be a valid date in format Y-m-d"]
            ],
            "Invalid_B_date_1" => [
                "data" => [
                    "B_date" => "04/23/2024",
                ],
                "expected_msg" => ["B_date" => "B_date must be a valid date and greater than 2024-04-23 and less than 2024-05-01"]
            ],
            "Invalid_B_date_2" => [
                "data" => [
                    "B_date" => "05/01/2024",
                ],
                "expected_msg" => ["B_date" => "B_date must be a valid date and greater than 2024-04-23 and less than 2024-05-01"]
            ],
            "Invalid_B_date_3" => [
                "data" => [
                    "B_date" => "04/24/2024 00:00:00",
                ],
                "expected_msg" => ["B_date" => "B_date must be a valid date in format m/d/Y"]
            ],
            "Invalid_C_date_1" => [
                "data" => [
                    "C_date" => "04/23/2024 01:01:01",
                ],
                "expected_msg" => ["C_date" => "C_date format m/d/Y H:i:s is not a valid date format"]
            ],
            "Invalid_C_date_2" => [
                "data" => [
                    "C_date" => "05/01/2024",
                ],
                "expected_msg" => ["C_date" => "C_date must be a valid date in format m/d/Y H:i:s"]
            ],
            "Invalid_C_date_3" => [
                "data" => [
                    "C_date" => "2024-04-23 01:01:01",
                ],
                "expected_msg" => ["C_date" => "C_date must be a valid date in format m/d/Y H:i:s"]
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
        ];

        return $method_info = [
            "rules" => $rules,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_date_greater_lessequal()
    {
        $rules = [
            "symbol" => [
                "A_date" => "optional|date><=[2024-04-23,2024-05-01]",
                "B_date" => "optional|date><=[2024-04-23,2024-05-01,m/d/Y]",
                "C_date" => "optional|date><=[2024-04-23 01:01:01,2024-05-01 12:12:12,m/d/Y H:i:s]",
            ],
            "method" => [
                "A_date" => "optional|date_greater_lessequal[2024-04-23,2024-05-01]",
                "B_date" => "optional|date_greater_lessequal[2024-04-23,2024-05-01,m/d/Y]",
                "C_date" => "optional|date_greater_lessequal[2024-04-23 01:01:01,2024-05-01 12:12:12,m/d/Y H:i:s]",
            ]
        ];

        $cases = [
            "Valid_A_date_1" => [
                "data" => [
                    "A_date" => "2024-04-24",
                ]
            ],
            "Valid_A_date_2" => [
                "data" => [
                    "A_date" => "2024-04-30",
                ]
            ],
            "Valid_A_date_3" => [
                "data" => [
                    "A_date" => "2024-05-01",
                ]
            ],
            "Valid_B_date_1" => [
                "data" => [
                    "B_date" => "04/24/2024",
                ]
            ],
            "Valid_B_date_2" => [
                "data" => [
                    "B_date" => "04/30/2024",
                ]
            ],
            "Valid_B_date_3" => [
                "data" => [
                    "B_date" => "05/01/2024",
                ]
            ],
            "Invalid_A_date_1" => [
                "data" => [
                    "A_date" => "2024-04-23",
                ],
                "expected_msg" => ["A_date" => "A_date must be a valid date and greater than 2024-04-23 and less than or equal to 2024-05-01"]
            ],
            "Invalid_A_date_2" => [
                "data" => [
                    "A_date" => "2024-05-02",
                ],
                "expected_msg" => ["A_date" => "A_date must be a valid date and greater than 2024-04-23 and less than or equal to 2024-05-01"]
            ],
            "Invalid_A_date_3" => [
                "data" => [
                    "A_date" => "2024-04-24 00:00:00",
                ],
                "expected_msg" => ["A_date" => "A_date must be a valid date in format Y-m-d"]
            ],
            "Invalid_B_date_1" => [
                "data" => [
                    "B_date" => "04/23/2024",
                ],
                "expected_msg" => ["B_date" => "B_date must be a valid date and greater than 2024-04-23 and less than or equal to 2024-05-01"]
            ],
            "Invalid_B_date_2" => [
                "data" => [
                    "B_date" => "05/02/2024",
                ],
                "expected_msg" => ["B_date" => "B_date must be a valid date and greater than 2024-04-23 and less than or equal to 2024-05-01"]
            ],
            "Invalid_B_date_3" => [
                "data" => [
                    "B_date" => "04/24/2024 00:00:00",
                ],
                "expected_msg" => ["B_date" => "B_date must be a valid date in format m/d/Y"]
            ],
            "Invalid_C_date_1" => [
                "data" => [
                    "C_date" => "04/23/2024 01:01:01",
                ],
                "expected_msg" => ["C_date" => "C_date format m/d/Y H:i:s is not a valid date format"]
            ],
            "Invalid_C_date_2" => [
                "data" => [
                    "C_date" => "05/01/2024",
                ],
                "expected_msg" => ["C_date" => "C_date must be a valid date in format m/d/Y H:i:s"]
            ],
            "Invalid_C_date_3" => [
                "data" => [
                    "C_date" => "2024-04-23 01:01:01",
                ],
                "expected_msg" => ["C_date" => "C_date must be a valid date in format m/d/Y H:i:s"]
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
        ];

        return $method_info = [
            "rules" => $rules,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_date_greaterequal_less()
    {
        $rules = [
            "symbol" => [
                "A_date" => "optional|date>=<[2024-04-23,2024-05-01]",
                "B_date" => "optional|date>=<[2024-04-23,2024-05-01,m/d/Y]",
                "C_date" => "optional|date>=<[2024-04-23 01:01:01,2024-05-01 12:12:12,m/d/Y H:i:s]",
            ],
            "method" => [
                "A_date" => "optional|date_greaterequal_less[2024-04-23,2024-05-01]",
                "B_date" => "optional|date_greaterequal_less[2024-04-23,2024-05-01,m/d/Y]",
                "C_date" => "optional|date_greaterequal_less[2024-04-23 01:01:01,2024-05-01 12:12:12,m/d/Y H:i:s]",
            ]
        ];

        $cases = [
            "Valid_A_date_1" => [
                "data" => [
                    "A_date" => "2024-04-23",
                ]
            ],
            "Valid_A_date_2" => [
                "data" => [
                    "A_date" => "2024-04-24",
                ]
            ],
            "Valid_A_date_3" => [
                "data" => [
                    "A_date" => "2024-04-30",
                ]
            ],
            "Valid_B_date_1" => [
                "data" => [
                    "B_date" => "04/23/2024",
                ]
            ],
            "Valid_B_date_2" => [
                "data" => [
                    "B_date" => "04/24/2024",
                ]
            ],
            "Valid_B_date_3" => [
                "data" => [
                    "B_date" => "04/30/2024",
                ]
            ],
            "Invalid_A_date_1" => [
                "data" => [
                    "A_date" => "2024-04-22",
                ],
                "expected_msg" => ["A_date" => "A_date must be a valid date and greater than or equal to 2024-04-23 and less than 2024-05-01"]
            ],
            "Invalid_A_date_2" => [
                "data" => [
                    "A_date" => "2024-05-01",
                ],
                "expected_msg" => ["A_date" => "A_date must be a valid date and greater than or equal to 2024-04-23 and less than 2024-05-01"]
            ],
            "Invalid_A_date_3" => [
                "data" => [
                    "A_date" => "2024-04-24 00:00:00",
                ],
                "expected_msg" => ["A_date" => "A_date must be a valid date in format Y-m-d"]
            ],
            "Invalid_B_date_1" => [
                "data" => [
                    "B_date" => "04/22/2024",
                ],
                "expected_msg" => ["B_date" => "B_date must be a valid date and greater than or equal to 2024-04-23 and less than 2024-05-01"]
            ],
            "Invalid_B_date_2" => [
                "data" => [
                    "B_date" => "05/01/2024",
                ],
                "expected_msg" => ["B_date" => "B_date must be a valid date and greater than or equal to 2024-04-23 and less than 2024-05-01"]
            ],
            "Invalid_B_date_3" => [
                "data" => [
                    "B_date" => "04/24/2024 00:00:00",
                ],
                "expected_msg" => ["B_date" => "B_date must be a valid date in format m/d/Y"]
            ],
            "Invalid_C_date_1" => [
                "data" => [
                    "C_date" => "04/23/2024 01:01:01",
                ],
                "expected_msg" => ["C_date" => "C_date format m/d/Y H:i:s is not a valid date format"]
            ],
            "Invalid_C_date_2" => [
                "data" => [
                    "C_date" => "05/01/2024",
                ],
                "expected_msg" => ["C_date" => "C_date must be a valid date in format m/d/Y H:i:s"]
            ],
            "Invalid_C_date_3" => [
                "data" => [
                    "C_date" => "2024-04-23 01:01:01",
                ],
                "expected_msg" => ["C_date" => "C_date must be a valid date in format m/d/Y H:i:s"]
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
        ];

        return $method_info = [
            "rules" => $rules,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_date_between()
    {
        $rules = [
            "symbol" => [
                "A_date" => "optional|date>=<=[2024-04-23,2024-05-01]",
                "B_date" => "optional|date>=<=[2024-04-23,2024-05-01,m/d/Y]",
                "C_date" => "optional|date>=<=[2024-04-23 01:01:01,2024-05-01 12:12:12,m/d/Y H:i:s]",
                "D_date" => "optional|date>=<=[2024-04,2024-10,Y-m]",
                "E_date" => "optional|date>=<=[04/23,05/01,m/d]",
                "F_date" => "optional|date>=<=[04,10,m]",
            ],
            "method" => [
                "A_date" => "optional|date_between[2024-04-23,2024-05-01]",
                "B_date" => "optional|date_between[2024-04-23,2024-05-01,m/d/Y]",
                "C_date" => "optional|date_between[2024-04-23 01:01:01,2024-05-01 12:12:12,m/d/Y H:i:s]",
                "D_date" => "optional|date_between[2024-04,2024-10,Y-m]",
                "E_date" => "optional|date_between[04/23,05/01,m/d]",
                "F_date" => "optional|date_between[04,10,m]",
            ]
        ];

        $cases = [
            "Valid_A_date_1" => [
                "data" => [
                    "A_date" => "2024-04-23",
                ]
            ],
            "Valid_A_date_2" => [
                "data" => [
                    "A_date" => "2024-04-24",
                ]
            ],
            "Valid_A_date_3" => [
                "data" => [
                    "A_date" => "2024-04-30",
                ]
            ],
            "Valid_A_date_4" => [
                "data" => [
                    "A_date" => "2024-05-01",
                ]
            ],
            "Valid_B_date_1" => [
                "data" => [
                    "B_date" => "04/23/2024",
                ]
            ],
            "Valid_B_date_2" => [
                "data" => [
                    "B_date" => "04/24/2024",
                ]
            ],
            "Valid_B_date_3" => [
                "data" => [
                    "B_date" => "04/30/2024",
                ]
            ],
            "Valid_B_date_4" => [
                "data" => [
                    "B_date" => "05/01/2024",
                ]
            ],
            "Valid_D_date_1" => [
                "data" => [
                    "D_date" => "2024-04",
                ]
            ],
            "Valid_D_date_2" => [
                "data" => [
                    "D_date" => "2024-05",
                ]
            ],
            "Valid_D_date_3" => [
                "data" => [
                    "D_date" => "2024-10",
                ]
            ],
            "Valid_D_date_4" => [
                "data" => [
                    "D_date" => "2024-09",
                ]
            ],
            "Valid_E_date_1" => [
                "data" => [
                    "E_date" => "04/23",
                ]
            ],
            "Valid_E_date_2" => [
                "data" => [
                    "E_date" => "04/24",
                ]
            ],
            "Valid_E_date_3" => [
                "data" => [
                    "E_date" => "05/01",
                ]
            ],
            "Valid_E_date_4" => [
                "data" => [
                    "E_date" => "04/30",
                ]
            ],
            "Valid_F_date_1" => [
                "data" => [
                    "F_date" => "04",
                ]
            ],
            "Valid_F_date_2" => [
                "data" => [
                    "F_date" => "05",
                ]
            ],
            "Valid_F_date_3" => [
                "data" => [
                    "F_date" => "10",
                ]
            ],
            "Valid_F_date_4" => [
                "data" => [
                    "F_date" => "09",
                ]
            ],
            "Invalid_A_date_1" => [
                "data" => [
                    "A_date" => "2024-04-22",
                ],
                "expected_msg" => ["A_date" => "A_date date must be between 2024-04-23 and 2024-05-01"]
            ],
            "Invalid_A_date_2" => [
                "data" => [
                    "A_date" => "2024-05-02",
                ],
                "expected_msg" => ["A_date" => "A_date date must be between 2024-04-23 and 2024-05-01"]
            ],
            "Invalid_A_date_3" => [
                "data" => [
                    "A_date" => "2024-04-24 00:00:00",
                ],
                "expected_msg" => ["A_date" => "A_date must be a valid date in format Y-m-d"]
            ],
            "Invalid_B_date_1" => [
                "data" => [
                    "B_date" => "04/22/2024",
                ],
                "expected_msg" => ["B_date" => "B_date date must be between 2024-04-23 and 2024-05-01"]
            ],
            "Invalid_B_date_2" => [
                "data" => [
                    "B_date" => "05/02/2024",
                ],
                "expected_msg" => ["B_date" => "B_date date must be between 2024-04-23 and 2024-05-01"]
            ],
            "Invalid_B_date_3" => [
                "data" => [
                    "B_date" => "04/24/2024 00:00:00",
                ],
                "expected_msg" => ["B_date" => "B_date must be a valid date in format m/d/Y"]
            ],
            "Invalid_C_date_1" => [
                "data" => [
                    "C_date" => "04/23/2024 01:01:01",
                ],
                "expected_msg" => ["C_date" => "C_date format m/d/Y H:i:s is not a valid date format"]
            ],
            "Invalid_C_date_2" => [
                "data" => [
                    "C_date" => "05/01/2024",
                ],
                "expected_msg" => ["C_date" => "C_date must be a valid date in format m/d/Y H:i:s"]
            ],
            "Invalid_C_date_3" => [
                "data" => [
                    "C_date" => "2024-04-23 01:01:01",
                ],
                "expected_msg" => ["C_date" => "C_date must be a valid date in format m/d/Y H:i:s"]
            ],
            "Invalid_D_date_1" => [
                "data" => [
                    "D_date" => "2024-13",
                ],
                "expected_msg" => ["D_date" => "D_date must be a valid date in format Y-m"]
            ],
            "Invalid_D_date_2" => [
                "data" => [
                    "D_date" => "2024-03",
                ],
                "expected_msg" => ["D_date" => "D_date date must be between 2024-04 and 2024-10"]
            ],
            "Invalid_D_date_3" => [
                "data" => [
                    "D_date" => "2024-11",
                ],
                "expected_msg" => ["D_date" => "D_date date must be between 2024-04 and 2024-10"]
            ],
            "Invalid_E_date_1" => [
                "data" => [
                    "E_date" => "13/23",
                ],
                "expected_msg" => ["E_date" => "E_date must be a valid date in format m/d"]
            ],
            "Invalid_E_date_2" => [
                "data" => [
                    "E_date" => "04/22",
                ],
                "expected_msg" => ["E_date" => "E_date date must be between 04/23 and 05/01"]
            ],
            "Invalid_E_date_3" => [
                "data" => [
                    "E_date" => "03/30",
                ],
                "expected_msg" => ["E_date" => "E_date date must be between 04/23 and 05/01"]
            ],
            "Invalid_E_date_4" => [
                "data" => [
                    "E_date" => "05/02",
                ],
                "expected_msg" => ["E_date" => "E_date date must be between 04/23 and 05/01"]
            ],
            "Invalid_E_date_5" => [
                "data" => [
                    "E_date" => "06/01",
                ],
                "expected_msg" => ["E_date" => "E_date date must be between 04/23 and 05/01"]
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
        ];

        return $method_info = [
            "rules" => $rules,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_time()
    {
        $rules = [
            "symbol" => [
                "A_time" => "optional|time",
                "B_time" => "optional|time[H_i_s]",
                "C_time" => "optional|time[H:i]",
                "D_time" => "optional|time[i:s]",
                "E_time" => "optional|time[s]",
                "A_wrong" => "optional|time[Y-m-d H:i:s]",
            ],
            "method" => [
                "A_time" => "optional|is_time",
                "B_time" => "optional|is_time[H_i_s]",
                "C_time" => "optional|is_time[H:i]",
                "D_time" => "optional|is_time[i:s]",
                "E_time" => "optional|is_time[s]",
                "A_wrong" => "optional|is_time[Y-m-d H:i:s]",
            ],
        ];

        $cases = [
            "Valid_A_time_1" => [
                "data" => [
                    "A_time" => "12:00:00",
                ]
            ],
            "Valid_B_time_1" => [
                "data" => [
                    "B_time" => "12_00_00",
                ]
            ],
            "Valid_C_time_1" => [
                "data" => [
                    "C_time" => "00:00",
                ]
            ],
            "Valid_C_time_1" => [
                "data" => [
                    "C_time" => "23:59",
                ]
            ],
            "Valid_D_time_1" => [
                "data" => [
                    "D_time" => "00:00",
                ]
            ],
            "Valid_D_time_1" => [
                "data" => [
                    "D_time" => "59:59",
                ]
            ],
            "Valid_E_time_1" => [
                "data" => [
                    "E_time" => "01",
                ]
            ],
            "Valid_E_time_2" => [
                "data" => [
                    "E_time" => "59",
                ]
            ],

            /**
             * Invalid cases
             */
            "Invalid_A_time_1" => [
                "data" => [
                    "A_time" => "12345678",
                ],
                "expected_msg" => ["A_time" => "A_time must be a valid time in format H:i:s"]
            ],
            "Invalid_A_time_2" => [
                "data" => [
                    "A_time" => "2024-04-23 12:00:00",
                ],
                "expected_msg" => ["A_time" => "A_time must be a valid time in format H:i:s"]
            ],
            "Invalid_A_time_3" => [
                "data" => [
                    "A_time" => "12:00:60",
                ],
                "expected_msg" => ["A_time" => "A_time must be a valid time in format H:i:s"]
            ],
            "Invalid_B_time_1" => [
                "data" => [
                    "B_time" => "00:00:00",
                ],
                "expected_msg" => ["B_time" => "B_time must be a valid time in format H_i_s"]
            ],
            "Invalid_B_time_2" => [
                "data" => [
                    "B_time" => "12_00_60",
                ],
                "expected_msg" => ["B_time" => "B_time must be a valid time in format H_i_s"]
            ],
            "Invalid_C_time_1" => [
                "data" => [
                    "C_time" => "12:00:00",
                ],
                "expected_msg" => ["C_time" => "C_time must be a valid time in format H:i"]
            ],
            "Invalid_C_time_2" => [
                "data" => [
                    "C_time" => "24:00",
                ],
                "expected_msg" => ["C_time" => "C_time must be a valid time in format H:i"]
            ],
            "Invalid_C_time_3" => [
                "data" => [
                    "C_time" => "23:60",
                ],
                "expected_msg" => ["C_time" => "C_time must be a valid time in format H:i"]
            ],
            "Invalid_D_time_1" => [
                "data" => [
                    "D_time" => "12:00:00",
                ],
                "expected_msg" => ["D_time" => "D_time must be a valid time in format i:s"]
            ],
            "Invalid_D_time_2" => [
                "data" => [
                    "D_time" => "60:00",
                ],
                "expected_msg" => ["D_time" => "D_time must be a valid time in format i:s"]
            ],
            "Invalid_D_time_3" => [
                "data" => [
                    "D_time" => "59:60",
                ],
                "expected_msg" => ["D_time" => "D_time must be a valid time in format i:s"]
            ],
            "Invalid_E_time_1" => [
                "data" => [
                    "E_time" => "0",
                ],
                "expected_msg" => ["E_time" => "E_time must be a valid time in format s"]
            ],
            "Invalid_E_time_2" => [
                "data" => [
                    "E_time" => "60",
                ],
                "expected_msg" => ["E_time" => "E_time must be a valid time in format s"]
            ],

            "Invalid_A_wrong_1" => [
                "data" => [
                    "A_wrong" => "2024-04-23 12:00:00",
                ],
                "expected_msg" => ["A_wrong" => "A_wrong format Y-m-d H:i:s is not a valid time format"]
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
        ];

        return $method_info = [
            "rules" => $rules,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_time_equal()
    {
        $rules = [
            "symbol" => [
                "A_time" => "optional|time=[01:01:01]",
                "B_time" => "optional|time=[01:01:00,H:i:s]",
                "C_time" => "optional|time=[01:01,H:i]",
                "A_exc" => "optional|time=[01:01:60]",
                "C_exc" => "optional|time=['0160',H:i]",
            ],
            "method" => [
                "A_time" => "optional|time_equal[01:01:01]",
                "B_time" => "optional|time_equal[01:01:00,H:i:s]",
                "C_time" => "optional|time_equal[01:01,H:i]",
                "A_exc" => "optional|time_equal[01:01:60]",
                "C_exc" => "optional|time_equal['0160',H:i]",
            ]
        ];

        $cases = [
            "Valid_A_time_1" => [
                "data" => [
                    "A_time" => '01:01:01',
                ]
            ],
            "Valid_B_time_1" => [
                "data" => [
                    "B_time" => '01:01:00',
                ]
            ],
            "Valid_C_time_1" => [
                "data" => [
                    "C_time" => '01:01',
                ]
            ],

            /**
             * Invalid cases
             */
            "Invalid_A_time_1" => [
                "data" => [
                    "A_time" => "010101",
                ],
                "expected_msg" => ["A_time" => "A_time must be a valid time in format H:i:s"]
            ],
            "Invalid_A_time_2" => [
                "data" => [
                    "A_time" => "01_01_01",
                ],
                "expected_msg" => ["A_time" => "A_time must be a valid time in format H:i:s"]
            ],
            "Invalid_A_time_3" => [
                "data" => [
                    "A_time" => "01:01:60",
                ],
                "expected_msg" => ["A_time" => "A_time must be a valid time in format H:i:s"]
            ],
            "Invalid_A_time_4" => [
                "data" => [
                    "A_time" => "01:60:01",
                ],
                "expected_msg" => ["A_time" => "A_time must be a valid time in format H:i:s"]
            ],
            "Invalid_A_time_5" => [
                "data" => [
                    "A_time" => "12:12:12",
                ],
                "expected_msg" => ["A_time" => "A_time must be a valid time and equal to 01:01:01"]
            ],
            "Invalid_B_time_1" => [
                "data" => [
                    "B_time" => "010101",
                ],
                "expected_msg" => ["B_time" => "B_time must be a valid time in format H:i:s"]
            ],
            "Invalid_B_time_2" => [
                "data" => [
                    "B_time" => "01_01_01",
                ],
                "expected_msg" => ["B_time" => "B_time must be a valid time in format H:i:s"]
            ],
            "Invalid_B_time_3" => [
                "data" => [
                    "B_time" => "01:01:60",
                ],
                "expected_msg" => ["B_time" => "B_time must be a valid time in format H:i:s"]
            ],
            "Invalid_B_time_4" => [
                "data" => [
                    "B_time" => "01:01",
                ],
                "expected_msg" => ["B_time" => "B_time must be a valid time in format H:i:s"]
            ],
            "Invalid_B_time_5" => [
                "data" => [
                    "B_time" => "12:12:12",
                ],
                "expected_msg" => ["B_time" => "B_time must be a valid time and equal to 01:01:00"]
            ],
            "Invalid_C_time_1" => [
                "data" => [
                    "C_time" => "01:01:00",
                ],
                "expected_msg" => ["C_time" => "C_time must be a valid time in format H:i"]
            ],
            "Invalid_C_time_2" => [
                "data" => [
                    "C_time" => "01:60",
                ],
                "expected_msg" => ["C_time" => "C_time must be a valid time in format H:i"]
            ],
            "Invalid_C_time_3" => [
                "data" => [
                    "C_time" => "01:00",
                ],
                "expected_msg" => ["C_time" => "C_time must be a valid time and equal to 01:01"]
            ],

            /**
             * Exception cases
             */
            "Exception_A_exc" => [
                "data" => [
                    "A_exc" => "01:01:01",
                ],
                "expected_msg" => '@field:A_exc, @method:time_equal - Parameter 01:01:60 is not a valid time'
            ],
            "Exception_C_exc" => [
                "data" => [
                    "C_exc" => "01:01",
                ],
                "expected_msg" => '@field:C_exc, @method:time_equal - Parameter 0160 is not a valid time with format H:i'
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
        ];

        return $method_info = [
            "rules" => $rules,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_time_not_equal()
    {
        $rules = [
            "symbol" => [
                "A_time" => "optional|time!=[01:01:01]",
                "B_time" => "optional|time!=[01:01,H:i]",
                "A_exc" => "optional|time!=[01:01:60]",
            ],
            "method" => [
                "A_time" => "optional|time_not_equal[01:01:01]",
                "B_time" => "optional|time_not_equal[01:01,H:i]",
                "A_exc" => "optional|time_not_equal[01:01:60]",
            ]
        ];

        $cases = [
            "Valid_A_time_1" => [
                "data" => [
                    "A_time" => "01:01:00",
                ]
            ],
            "Valid_B_time_1" => [
                "data" => [
                    "B_time" => "01:02",
                ]
            ],
            "Invalid_A_time_1" => [
                "data" => [
                    "A_time" => "01:01",
                ],
                "expected_msg" => ["A_time" => "A_time must be a valid time in format H:i:s"]
            ],
            "Invalid_A_time_2" => [
                "data" => [
                    "A_time" => "01:01:60",
                ],
                "expected_msg" => ["A_time" => "A_time must be a valid time in format H:i:s"]
            ],
            "Invalid_A_time_3" => [
                "data" => [
                    "A_time" => "01:01:01",
                ],
                "expected_msg" => ["A_time" => "A_time must be a valid time and not equal to 01:01:01"]
            ],
            "Invalid_B_time_1" => [
                "data" => [
                    "B_time" => "01:01:01",
                ],
                "expected_msg" => ["B_time" => "B_time must be a valid time in format H:i"]
            ],
            "Invalid_B_time_2" => [
                "data" => [
                    "B_time" => "01:01:60",
                ],
                "expected_msg" => ["B_time" => "B_time must be a valid time in format H:i"]
            ],
            "Invalid_B_time_3" => [
                "data" => [
                    "B_time" => "01:01",
                ],
                "expected_msg" => ["B_time" => "B_time must be a valid time and not equal to 01:01"]
            ],
            "Exception_A_exc" => [
                "data" => [
                    "A_exc" => "01:01:01",
                ],
                "expected_msg" => '@field:A_exc, @method:time_not_equal - Parameter 01:01:60 is not a valid time'
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
        ];

        return $method_info = [
            "rules" => $rules,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_time_greater_than()
    {
        $rules = [
            "symbol" => [
                "A_time" => "optional|time>[01:01:00]",
                "B_time" => "optional|time>[01:01,H:i]",
                "C_time" => "optional|time>[01,H]",
                "A_exc" => "optional|time>[01:01:60]",
            ],
            "method" => [
                "A_time" => "optional|time_greater_than[01:01:00]",
                "B_time" => "optional|time_greater_than[01:01,H:i]",
                "C_time" => "optional|time_greater_than[01,H]",
                "A_exc" => "optional|time_greater_than[01:01:60]",
            ]
        ];

        $cases = [
            "Valid_A_time_1" => [
                "data" => [
                    "A_time" => "01:01:01",
                ]
            ],
            "Valid_B_time_1" => [
                "data" => [
                    "B_time" => "01:02",
                ]
            ],
            "Valid_C_time_1" => [
                "data" => [
                    "C_time" => "02",
                ]
            ],
            "Invalid_A_time_1" => [
                "data" => [
                    "A_time" => "01:02",
                ],
                "expected_msg" => ["A_time" => "A_time must be a valid time in format H:i:s"]
            ],
            "Invalid_A_time_2" => [
                "data" => [
                    "A_time" => "01:00:59",
                ],
                "expected_msg" => ["A_time" => "A_time must be a valid time and greater than 01:01:00"]
            ],
            "Invalid_B_time_1" => [
                "data" => [
                    "B_time" => "01:01:00",
                ],
                "expected_msg" => ["B_time" => "B_time must be a valid time in format H:i"]
            ],
            "Invalid_B_time_2" => [
                "data" => [
                    "B_time" => "01:01",
                ],
                "expected_msg" => ["B_time" => "B_time must be a valid time and greater than 01:01"]
            ],
            "Exception_A_exc" => [
                "data" => [
                    "A_exc" => "01:01:01",
                ],
                "expected_msg" => '@field:A_exc, @method:time_greater_than - Parameter 01:01:60 is not a valid time'
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
        ];

        return $method_info = [
            "rules" => $rules,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_time_greater_equal()
    {
        $rules = [
            "symbol" => [
                "A_time" => "optional|time>=[01:01:00]",
                "B_time" => "optional|time>=[01:01,H:i]",
                "C_time" => "optional|time>=[01,H]",
                "A_exc" => "optional|time>=[01:01:60]",
            ],
            "method" => [
                "A_time" => "optional|time_greater_equal[01:01:00]",
                "B_time" => "optional|time_greater_equal[01:01,H:i]",
                "C_time" => "optional|time_greater_equal[01,H]",
                "A_exc" => "optional|time_greater_equal[01:01:60]",
            ]
        ];

        $cases = [
            "Valid_A_time_1" => [
                "data" => [
                    "A_time" => "01:01:00",
                ]
            ],
            "Valid_A_time_1" => [
                "data" => [
                    "A_time" => "01:01:01",
                ]
            ],
            "Valid_B_time_1" => [
                "data" => [
                    "B_time" => "01:01",
                ]
            ],
            "Valid_B_time_2" => [
                "data" => [
                    "B_time" => "01:02",
                ]
            ],
            "Valid_C_time_1" => [
                "data" => [
                    "C_time" => "01",
                ]
            ],
            "Valid_C_time_2" => [
                "data" => [
                    "C_time" => "02",
                ]
            ],
            "Invalid_A_time_1" => [
                "data" => [
                    "A_time" => "01:02",
                ],
                "expected_msg" => ["A_time" => "A_time must be a valid time in format H:i:s"]
            ],
            "Invalid_A_time_2" => [
                "data" => [
                    "A_time" => "01:00:59",
                ],
                "expected_msg" => ["A_time" => "A_time must be a valid time and greater than or equal to 01:01:00"]
            ],
            "Invalid_A_time_2" => [
                "data" => [
                    "A_time" => "00:59:59",
                ],
                "expected_msg" => ["A_time" => "A_time must be a valid time and greater than or equal to 01:01:00"]
            ],
            "Invalid_B_time_1" => [
                "data" => [
                    "B_time" => "01:01:00",
                ],
                "expected_msg" => ["B_time" => "B_time must be a valid time in format H:i"]
            ],
            "Invalid_B_time_2" => [
                "data" => [
                    "B_time" => "01:00",
                ],
                "expected_msg" => ["B_time" => "B_time must be a valid time and greater than or equal to 01:01"]
            ],
            "Invalid_B_time_3" => [
                "data" => [
                    "B_time" => "00:59",
                ],
                "expected_msg" => ["B_time" => "B_time must be a valid time and greater than or equal to 01:01"]
            ],
            "Exception_A_exc" => [
                "data" => [
                    "A_exc" => "01:01:01",
                ],
                "expected_msg" => '@field:A_exc, @method:time_greater_equal - Parameter 01:01:60 is not a valid time'
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
        ];

        return $method_info = [
            "rules" => $rules,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_time_less_than()
    {
        $rules = [
            "symbol" => [
                "A_time" => "optional|time<[01:01:00]",
                "B_time" => "optional|time<[01:01,i:s]",
                "C_time" => "optional|time<['01',s]",
                "A_exc" => "optional|time<[01:01:60]",
            ],
            "method" => [
                "A_time" => "optional|time_less_than[01:01:00]",
                "B_time" => "optional|time_less_than[01:01,i:s]",
                "C_time" => "optional|time_less_than['01',s]",
                "A_exc" => "optional|time_less_than[01:01:60]",
            ]
        ];

        $cases = [
            "Valid_A_time_1" => [
                "data" => [
                    "A_time" => "01:00:59",
                ]
            ],
            "Valid_B_time_1" => [
                "data" => [
                    "B_time" => "01:00",
                ]
            ],
            "Valid_C_time_1" => [
                "data" => [
                    "C_time" => "00",
                ]
            ],
            "Invalid_A_time_1" => [
                "data" => [
                    "A_time" => "01:02",
                ],
                "expected_msg" => ["A_time" => "A_time must be a valid time in format H:i:s"]
            ],
            "Invalid_A_time_2" => [
                "data" => [
                    "A_time" => "02:00:00",
                ],
                "expected_msg" => ["A_time" => "A_time must be a valid time and less than 01:01:00"]
            ],
            "Invalid_B_time_1" => [
                "data" => [
                    "B_time" => "01:01:00",
                ],
                "expected_msg" => ["B_time" => "B_time must be a valid time in format i:s"]
            ],
            "Invalid_B_time_2" => [
                "data" => [
                    "B_time" => "01:02",
                ],
                "expected_msg" => ["B_time" => "B_time must be a valid time and less than 01:01"]
            ],
            "Invalid_C_time_1" => [
                "data" => [
                    "C_time" => "01:01:00",
                ],
                "expected_msg" => ["C_time" => "C_time must be a valid time in format s"]
            ],
            "Invalid_C_time_2" => [
                "data" => [
                    "C_time" => "01",
                ],
                "expected_msg" => ["C_time" => "C_time must be a valid time and less than 01"]
            ],
            "Exception_A_exc" => [
                "data" => [
                    "A_exc" => "01:01:01",
                ],
                "expected_msg" => '@field:A_exc, @method:time_less_than - Parameter 01:01:60 is not a valid time'
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
        ];

        return $method_info = [
            "rules" => $rules,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_time_less_equal()
    {
        $rules = [
            "symbol" => [
                "A_time" => "optional|time<=[01:01:00]",
                "B_time" => "optional|time<=[01:01,i:s]",
                "C_time" => "optional|time<=['01',s]",
                "A_exc" => "optional|time<=[01:01:60]",
            ],
            "method" => [
                "A_time" => "optional|time_less_equal[01:01:00]",
                "B_time" => "optional|time_less_equal[01:01,i:s]",
                "C_time" => "optional|time_less_equal['01',s]",
                "A_exc" => "optional|time_less_equal[01:01:60]",
            ]
        ];

        $cases = [
            "Valid_A_time_1" => [
                "data" => [
                    "A_time" => "01:00:59",
                ]
            ],
            "Valid_A_time_2" => [
                "data" => [
                    "A_time" => "01:01:00",
                ]
            ],
            "Valid_B_time_1" => [
                "data" => [
                    "B_time" => "01:00",
                ]
            ],
            "Valid_B_time_2" => [
                "data" => [
                    "B_time" => "01:01",
                ]
            ],
            "Valid_C_time_1" => [
                "data" => [
                    "C_time" => "00",
                ]
            ],
            "Valid_C_time_2" => [
                "data" => [
                    "C_time" => "01",
                ]
            ],
            "Invalid_A_time_1" => [
                "data" => [
                    "A_time" => "01:02",
                ],
                "expected_msg" => ["A_time" => "A_time must be a valid time in format H:i:s"]
            ],
            "Invalid_A_time_2" => [
                "data" => [
                    "A_time" => "02:00:00",
                ],
                "expected_msg" => ["A_time" => "A_time must be a valid time and less than or equal to 01:01:00"]
            ],
            "Invalid_A_time_3" => [
                "data" => [
                    "A_time" => "01:01:01",
                ],
                "expected_msg" => ["A_time" => "A_time must be a valid time and less than or equal to 01:01:00"]
            ],
            "Invalid_B_time_1" => [
                "data" => [
                    "B_time" => "01:01:00",
                ],
                "expected_msg" => ["B_time" => "B_time must be a valid time in format i:s"]
            ],
            "Invalid_B_time_2" => [
                "data" => [
                    "B_time" => "01:02",
                ],
                "expected_msg" => ["B_time" => "B_time must be a valid time and less than or equal to 01:01"]
            ],
            "Invalid_C_time_1" => [
                "data" => [
                    "C_time" => "01:01:00",
                ],
                "expected_msg" => ["C_time" => "C_time must be a valid time in format s"]
            ],
            "Invalid_C_time_2" => [
                "data" => [
                    "C_time" => "02",
                ],
                "expected_msg" => ["C_time" => "C_time must be a valid time and less than or equal to 01"]
            ],
            "Exception_A_exc" => [
                "data" => [
                    "A_exc" => "01:01:01",
                ],
                "expected_msg" => '@field:A_exc, @method:time_less_equal - Parameter 01:01:60 is not a valid time'
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
        ];

        return $method_info = [
            "rules" => $rules,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_time_greater_less()
    {
        $rules = [
            "symbol" => [
                "A_time" => "optional|time><[01:01:00,12:12:00]",
                "B_time" => "optional|time><[01:01,12:12,H:i]",
                "C_time" => "optional|time><['01',12,H]",
                "D_time" => "optional|time><[22:00:00,02:00:00]",
                "A_exc" => "optional|time><[01:60,60:00,H:i]",
                "B_exc" => "optional|time><[01:01,60:00,H:i]",
            ],
            "method" => [
                "A_time" => "optional|time_greater_less[01:01:00,12:12:00]",
                "B_time" => "optional|time_greater_less[01:01,12:12,H:i]",
                "C_time" => "optional|time_greater_less['01',12,H]",
                "D_time" => "optional|time_greater_less[22:00:00,02:00:00]",
                "A_exc" => "optional|time_greater_less[01:60,60:00,H:i]",
                "B_exc" => "optional|time_greater_less[01:01,60:00,H:i]",
            ]
        ];

        $cases = [
            "Valid_A_time_1" => [
                "data" => [
                    "A_time" => "01:01:01",
                ]
            ],
            "Valid_A_time_2" => [
                "data" => [
                    "A_time" => "12:11:59",
                ]
            ],
            "Valid_B_time_1" => [
                "data" => [
                    "B_time" => "01:02",
                ]
            ],
            "Valid_B_time_2" => [
                "data" => [
                    "B_time" => "11:59",
                ]
            ],
            "Valid_C_time_1" => [
                "data" => [
                    "C_time" => "02",
                ]
            ],
            "Valid_C_time_2" => [
                "data" => [
                    "C_time" => "11",
                ]
            ],
            "Valid_D_time_1" => [
                "data" => [
                    "D_time" => "22:00:01",
                ]
            ],
            "Valid_D_time_2" => [
                "data" => [
                    "D_time" => "23:59:59",
                ]
            ],
            "Valid_D_time_3" => [
                "data" => [
                    "D_time" => "00:00:00",
                ]
            ],
            "Valid_D_time_4" => [
                "data" => [
                    "D_time" => "01:59:59",
                ]
            ],
            "Invalid_A_time_1" => [
                "data" => [
                    "A_time" => "01:01:60",
                ],
                "expected_msg" => ["A_time" => "A_time must be a valid time in format H:i:s"]
            ],
            "Invalid_A_time_2" => [
                "data" => [
                    "A_time" => "01:01:00",
                ],
                "expected_msg" => ["A_time" => "A_time must be a valid time and greater than 01:01:00 and less than 12:12:00"]
            ],
            "Invalid_A_time_3" => [
                "data" => [
                    "A_time" => "01:00:59",
                ],
                "expected_msg" => ["A_time" => "A_time must be a valid time and greater than 01:01:00 and less than 12:12:00"]
            ],
            "Invalid_A_time_4" => [
                "data" => [
                    "A_time" => "12:12:00",
                ],
                "expected_msg" => ["A_time" => "A_time must be a valid time and greater than 01:01:00 and less than 12:12:00"]
            ],
            "Invalid_A_time_5" => [
                "data" => [
                    "A_time" => "12:12:01",
                ],
                "expected_msg" => ["A_time" => "A_time must be a valid time and greater than 01:01:00 and less than 12:12:00"]
            ],
            "Invalid_B_time_1" => [
                "data" => [
                    "B_time" => "01:60",
                ],
                "expected_msg" => ["B_time" => "B_time must be a valid time in format H:i"]
            ],
            "Invalid_B_time_2" => [
                "data" => [
                    "B_time" => "01:01",
                ],
                "expected_msg" => ["B_time" => "B_time must be a valid time and greater than 01:01 and less than 12:12"]
            ],
            "Invalid_B_time_3" => [
                "data" => [
                    "B_time" => "01:00",
                ],
                "expected_msg" => ["B_time" => "B_time must be a valid time and greater than 01:01 and less than 12:12"]
            ],
            "Invalid_B_time_4" => [
                "data" => [
                    "B_time" => "12:12",
                ],
                "expected_msg" => ["B_time" => "B_time must be a valid time and greater than 01:01 and less than 12:12"]
            ],
            "Invalid_B_time_5" => [
                "data" => [
                    "B_time" => "12:13",
                ],
                "expected_msg" => ["B_time" => "B_time must be a valid time and greater than 01:01 and less than 12:12"]
            ],
            "Invalid_C_time_1" => [
                "data" => [
                    "C_time" => "24",
                ],
                "expected_msg" => ["C_time" => "C_time must be a valid time in format H"]
            ],
            "Invalid_C_time_2" => [
                "data" => [
                    "C_time" => "01",
                ],
                "expected_msg" => ["C_time" => "C_time must be a valid time and greater than 01 and less than 12"]
            ],
            "Invalid_C_time_3" => [
                "data" => [
                    "C_time" => "00",
                ],
                "expected_msg" => ["C_time" => "C_time must be a valid time and greater than 01 and less than 12"]
            ],
            "Invalid_C_time_4" => [
                "data" => [
                    "C_time" => "12",
                ],
                "expected_msg" => ["C_time" => "C_time must be a valid time and greater than 01 and less than 12"]
            ],
            "Invalid_C_time_5" => [
                "data" => [
                    "C_time" => "13",
                ],
                "expected_msg" => ["C_time" => "C_time must be a valid time and greater than 01 and less than 12"]
            ],
            "Invalid_D_time_1" => [
                "data" => [
                    "D_time" => "22:00:00",
                ],
                "expected_msg" => ["D_time" => "D_time must be a valid time and greater than 22:00:00 and less than 02:00:00"]
            ],
            "Invalid_D_time_2" => [
                "data" => [
                    "D_time" => "02:00:00",
                ],
                "expected_msg" => ["D_time" => "D_time must be a valid time and greater than 22:00:00 and less than 02:00:00"]
            ],
            "Invalid_D_time_3" => [
                "data" => [
                    "D_time" => "21:00:00",
                ],
                "expected_msg" => ["D_time" => "D_time must be a valid time and greater than 22:00:00 and less than 02:00:00"]
            ],
            "Invalid_D_time_4" => [
                "data" => [
                    "D_time" => "03:00:00",
                ],
                "expected_msg" => ["D_time" => "D_time must be a valid time and greater than 22:00:00 and less than 02:00:00"]
            ],
            "Exception_A_exc" => [
                "data" => [
                    "A_exc" => "01:01",
                ],
                "expected_msg" => '@field:A_exc, @method:time_greater_less - Parameter 01:60 is not a valid time with format H:i'
            ],
            "Exception_B_exc" => [
                "data" => [
                    "B_exc" => "01:01",
                ],
                "expected_msg" => '@field:B_exc, @method:time_greater_less - Parameter 60:00 is not a valid time with format H:i'
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
        ];

        return $method_info = [
            "rules" => $rules,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_time_greater_lessequal()
    {
        $rules = [
            "symbol" => [
                "A_time" => "optional|time><=[01:01:00,12:12:00]",
                "B_time" => "optional|time><=[01:01,12:12,H:i]",
                "C_time" => "optional|time><=['01',12,H]",
                "D_time" => "optional|time><=[22:00:00,02:00:00]",
                "A_exc" => "optional|time><=[01:60,60:00,H:i]",
                "B_exc" => "optional|time><=[01:01,60:00,H:i]",
            ],
            "method" => [
                "A_time" => "optional|time_greater_lessequal[01:01:00,12:12:00]",
                "B_time" => "optional|time_greater_lessequal[01:01,12:12,H:i]",
                "C_time" => "optional|time_greater_lessequal['01',12,H]",
                "D_time" => "optional|time_greater_lessequal[22:00:00,02:00:00]",
                "A_exc" => "optional|time_greater_lessequal[01:60,60:00,H:i]",
                "B_exc" => "optional|time_greater_lessequal[01:01,60:00,H:i]",
            ]
        ];

        $cases = [
            "Valid_A_time_1" => [
                "data" => [
                    "A_time" => "01:01:01",
                ]
            ],
            "Valid_A_time_2" => [
                "data" => [
                    "A_time" => "12:11:59",
                ]
            ],
            "Valid_A_time_3" => [
                "data" => [
                    "A_time" => "12:12:00",
                ]
            ],
            "Valid_B_time_1" => [
                "data" => [
                    "B_time" => "01:02",
                ]
            ],
            "Valid_B_time_2" => [
                "data" => [
                    "B_time" => "12:11",
                ]
            ],
            "Valid_B_time_3" => [
                "data" => [
                    "B_time" => "12:12",
                ]
            ],
            "Valid_C_time_1" => [
                "data" => [
                    "C_time" => "02",
                ]
            ],
            "Valid_C_time_2" => [
                "data" => [
                    "C_time" => "11",
                ]
            ],
            "Valid_C_time_3" => [
                "data" => [
                    "C_time" => "12",
                ]
            ],
            "Valid_D_time_1" => [
                "data" => [
                    "D_time" => "22:00:01",
                ]
            ],
            "Valid_D_time_2" => [
                "data" => [
                    "D_time" => "23:59:59",
                ]
            ],
            "Valid_D_time_3" => [
                "data" => [
                    "D_time" => "00:00:00",
                ]
            ],
            "Valid_D_time_4" => [
                "data" => [
                    "D_time" => "02:00:00",
                ]
            ],
            "Invalid_A_time_1" => [
                "data" => [
                    "A_time" => "01:01:60",
                ],
                "expected_msg" => ["A_time" => "A_time must be a valid time in format H:i:s"]
            ],
            "Invalid_A_time_2" => [
                "data" => [
                    "A_time" => "01:01:00",
                ],
                "expected_msg" => ["A_time" => "A_time must be a valid time and greater than 01:01:00 and less than or equal to 12:12:00"]
            ],
            "Invalid_A_time_3" => [
                "data" => [
                    "A_time" => "01:00:59",
                ],
                "expected_msg" => ["A_time" => "A_time must be a valid time and greater than 01:01:00 and less than or equal to 12:12:00"]
            ],
            "Invalid_A_time_4" => [
                "data" => [
                    "A_time" => "12:12:01",
                ],
                "expected_msg" => ["A_time" => "A_time must be a valid time and greater than 01:01:00 and less than or equal to 12:12:00"]
            ],
            "Invalid_A_time_5" => [
                "data" => [
                    "A_time" => "12:12:02",
                ],
                "expected_msg" => ["A_time" => "A_time must be a valid time and greater than 01:01:00 and less than or equal to 12:12:00"]
            ],
            "Invalid_B_time_1" => [
                "data" => [
                    "B_time" => "01:60",
                ],
                "expected_msg" => ["B_time" => "B_time must be a valid time in format H:i"]
            ],
            "Invalid_B_time_2" => [
                "data" => [
                    "B_time" => "01:01",
                ],
                "expected_msg" => ["B_time" => "B_time must be a valid time and greater than 01:01 and less than or equal to 12:12"]
            ],
            "Invalid_B_time_3" => [
                "data" => [
                    "B_time" => "01:00",
                ],
                "expected_msg" => ["B_time" => "B_time must be a valid time and greater than 01:01 and less than or equal to 12:12"]
            ],
            "Invalid_B_time_4" => [
                "data" => [
                    "B_time" => "12:13",
                ],
                "expected_msg" => ["B_time" => "B_time must be a valid time and greater than 01:01 and less than or equal to 12:12"]
            ],
            "Invalid_B_time_5" => [
                "data" => [
                    "B_time" => "13:12",
                ],
                "expected_msg" => ["B_time" => "B_time must be a valid time and greater than 01:01 and less than or equal to 12:12"]
            ],
            "Invalid_C_time_1" => [
                "data" => [
                    "C_time" => "24",
                ],
                "expected_msg" => ["C_time" => "C_time must be a valid time in format H"]
            ],
            "Invalid_C_time_2" => [
                "data" => [
                    "C_time" => "01",
                ],
                "expected_msg" => ["C_time" => "C_time must be a valid time and greater than 01 and less than or equal to 12"]
            ],
            "Invalid_C_time_3" => [
                "data" => [
                    "C_time" => "00",
                ],
                "expected_msg" => ["C_time" => "C_time must be a valid time and greater than 01 and less than or equal to 12"]
            ],
            "Invalid_C_time_4" => [
                "data" => [
                    "C_time" => "13",
                ],
                "expected_msg" => ["C_time" => "C_time must be a valid time and greater than 01 and less than or equal to 12"]
            ],
            "Invalid_C_time_5" => [
                "data" => [
                    "C_time" => "14",
                ],
                "expected_msg" => ["C_time" => "C_time must be a valid time and greater than 01 and less than or equal to 12"]
            ],
            "Invalid_D_time_1" => [
                "data" => [
                    "D_time" => "22:00:00",
                ],
                "expected_msg" => ["D_time" => "D_time must be a valid time and greater than 22:00:00 and less than or equal to 02:00:00"]
            ],
            "Invalid_D_time_2" => [
                "data" => [
                    "D_time" => "03:00:00",
                ],
                "expected_msg" => ["D_time" => "D_time must be a valid time and greater than 22:00:00 and less than or equal to 02:00:00"]
            ],
            "Invalid_D_time_3" => [
                "data" => [
                    "D_time" => "21:59:59",
                ],
                "expected_msg" => ["D_time" => "D_time must be a valid time and greater than 22:00:00 and less than or equal to 02:00:00"]
            ],
            "Exception_A_exc" => [
                "data" => [
                    "A_exc" => "01:01",
                ],
                "expected_msg" => '@field:A_exc, @method:time_greater_lessequal - Parameter 01:60 is not a valid time with format H:i'
            ],
            "Exception_B_exc" => [
                "data" => [
                    "B_exc" => "01:01",
                ],
                "expected_msg" => '@field:B_exc, @method:time_greater_lessequal - Parameter 60:00 is not a valid time with format H:i'
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
        ];

        return $method_info = [
            "rules" => $rules,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_time_greaterequal_less()
    {
        $rules = [
            "symbol" => [
                "A_time" => "optional|time>=<[01:01:00,12:12:00]",
                "B_time" => "optional|time>=<[01:01,12:12,i:s]",
                "C_time" => "optional|time>=<['01',12,i]",
                "D_time" => "optional|time>=<[22:00:00,02:00:00]",
                "A_exc" => "optional|time>=<[01:60,60:00,i:s]",
                "B_exc" => "optional|time>=<[01:01,60:00,H:i]",
            ],
            "method" => [
                "A_time" => "optional|time_greaterequal_less[01:01:00,12:12:00]",
                "B_time" => "optional|time_greaterequal_less[01:01,12:12,i:s]",
                "C_time" => "optional|time_greaterequal_less['01',12,i]",
                "D_time" => "optional|time_greaterequal_less[22:00:00,02:00:00]",
                "A_exc" => "optional|time_greaterequal_less[01:60,60:00,i:s]",
                "B_exc" => "optional|time_greaterequal_less[01:01,60:00,H:i]",
            ]
        ];

        $cases = [
            "Valid_A_time_1" => [
                "data" => [
                    "A_time" => "01:01:01",
                ]
            ],
            "Valid_A_time_2" => [
                "data" => [
                    "A_time" => "01:01:00",
                ]
            ],
            "Valid_A_time_3" => [
                "data" => [
                    "A_time" => "12:11:59",
                ]
            ],
            "Valid_B_time_1" => [
                "data" => [
                    "B_time" => "01:02",
                ]
            ],
            "Valid_B_time_2" => [
                "data" => [
                    "B_time" => "01:01",
                ]
            ],
            "Valid_B_time_3" => [
                "data" => [
                    "B_time" => "12:11",
                ]
            ],
            "Valid_C_time_1" => [
                "data" => [
                    "C_time" => "02",
                ]
            ],
            "Valid_C_time_2" => [
                "data" => [
                    "C_time" => "01",
                ]
            ],
            "Valid_C_time_3" => [
                "data" => [
                    "C_time" => "11",
                ]
            ],
            "Valid_D_time_1" => [
                "data" => [
                    "D_time" => "22:00:00",
                ]
            ],
            "Valid_D_time_2" => [
                "data" => [
                    "D_time" => "23:59:59",
                ]
            ],
            "Valid_D_time_3" => [
                "data" => [
                    "D_time" => "00:00:00",
                ]
            ],
            "Valid_D_time_4" => [
                "data" => [
                    "D_time" => "01:59:59",
                ]
            ],
            "Invalid_A_time_1" => [
                "data" => [
                    "A_time" => "01:01:60",
                ],
                "expected_msg" => ["A_time" => "A_time must be a valid time in format H:i:s"]
            ],
            "Invalid_A_time_2" => [
                "data" => [
                    "A_time" => "01:00:59",
                ],
                "expected_msg" => ["A_time" => "A_time must be a valid time and greater than or equal to 01:01:00 and less than 12:12:00"]
            ],
            "Invalid_A_time_3" => [
                "data" => [
                    "A_time" => "00:01:00",
                ],
                "expected_msg" => ["A_time" => "A_time must be a valid time and greater than or equal to 01:01:00 and less than 12:12:00"]
            ],
            "Invalid_A_time_4" => [
                "data" => [
                    "A_time" => "12:12:01",
                ],
                "expected_msg" => ["A_time" => "A_time must be a valid time and greater than or equal to 01:01:00 and less than 12:12:00"]
            ],
            "Invalid_A_time_5" => [
                "data" => [
                    "A_time" => "12:12:02",
                ],
                "expected_msg" => ["A_time" => "A_time must be a valid time and greater than or equal to 01:01:00 and less than 12:12:00"]
            ],
            "Invalid_B_time_1" => [
                "data" => [
                    "B_time" => "01:60",
                ],
                "expected_msg" => ["B_time" => "B_time must be a valid time in format i:s"]
            ],
            "Invalid_B_time_2" => [
                "data" => [
                    "B_time" => "01:00",
                ],
                "expected_msg" => ["B_time" => "B_time must be a valid time and greater than or equal to 01:01 and less than 12:12"]
            ],
            "Invalid_B_time_3" => [
                "data" => [
                    "B_time" => "00:59",
                ],
                "expected_msg" => ["B_time" => "B_time must be a valid time and greater than or equal to 01:01 and less than 12:12"]
            ],
            "Invalid_B_time_4" => [
                "data" => [
                    "B_time" => "12:13",
                ],
                "expected_msg" => ["B_time" => "B_time must be a valid time and greater than or equal to 01:01 and less than 12:12"]
            ],
            "Invalid_B_time_5" => [
                "data" => [
                    "B_time" => "13:12",
                ],
                "expected_msg" => ["B_time" => "B_time must be a valid time and greater than or equal to 01:01 and less than 12:12"]
            ],
            "Invalid_C_time_1" => [
                "data" => [
                    "C_time" => "60",
                ],
                "expected_msg" => ["C_time" => "C_time must be a valid time in format i"]
            ],
            "Invalid_C_time_2" => [
                "data" => [
                    "C_time" => "00",
                ],
                "expected_msg" => ["C_time" => "C_time must be a valid time and greater than or equal to 01 and less than 12"]
            ],
            "Invalid_C_time_3" => [
                "data" => [
                    "C_time" => "12",
                ],
                "expected_msg" => ["C_time" => "C_time must be a valid time and greater than or equal to 01 and less than 12"]
            ],
            "Invalid_C_time_4" => [
                "data" => [
                    "C_time" => "13",
                ],
                "expected_msg" => ["C_time" => "C_time must be a valid time and greater than or equal to 01 and less than 12"]
            ],
            "Invalid_C_time_5" => [
                "data" => [
                    "C_time" => "14",
                ],
                "expected_msg" => ["C_time" => "C_time must be a valid time and greater than or equal to 01 and less than 12"]
            ],
            "Invalid_D_time_1" => [
                "data" => [
                    "D_time" => "21:00:00",
                ],
                "expected_msg" => ["D_time" => "D_time must be a valid time and greater than or equal to 22:00:00 and less than 02:00:00"]
            ],
            "Invalid_D_time_2" => [
                "data" => [
                    "D_time" => "02:00:00",
                ],
                "expected_msg" => ["D_time" => "D_time must be a valid time and greater than or equal to 22:00:00 and less than 02:00:00"]
            ],
            "Invalid_D_time_2" => [
                "data" => [
                    "D_time" => "03:00:00",
                ],
                "expected_msg" => ["D_time" => "D_time must be a valid time and greater than or equal to 22:00:00 and less than 02:00:00"]
            ],
            "Exception_A_exc" => [
                "data" => [
                    "A_exc" => "01:01",
                ],
                "expected_msg" => '@field:A_exc, @method:time_greaterequal_less - Parameter 01:60 is not a valid time with format i:s'
            ],
            "Exception_B_exc" => [
                "data" => [
                    "B_exc" => "01:01",
                ],
                "expected_msg" => '@field:B_exc, @method:time_greaterequal_less - Parameter 60:00 is not a valid time with format H:i'
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
        ];

        return $method_info = [
            "rules" => $rules,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_time_between()
    {
        $rules = [
            "symbol" => [
                "A_time" => "optional|time>=<=[01:01:00,12:12:00]",
                "B_time" => "optional|time>=<=[01:01,12:12,i:s]",
                "C_time" => "optional|time>=<=['01',12,i]",
                "D_time" => "optional|time>=<=[22:00:00,02:00:00]",
                "A_exc" => "optional|time>=<=[01:60,60:00,i:s]",
                "B_exc" => "optional|time>=<=[01:01,60:00,H:i]",
            ],
            "method" => [
                "A_time" => "optional|time_between[01:01:00,12:12:00]",
                "B_time" => "optional|time_between[01:01,12:12,i:s]",
                "C_time" => "optional|time_between['01',12,i]",
                "D_time" => "optional|time_between[22:00:00,02:00:00]",
                "A_exc" => "optional|time_between[01:60,60:00,i:s]",
                "B_exc" => "optional|time_between[01:01,60:00,H:i]",
            ]
        ];

        $cases = [
            "Valid_A_time_1" => [
                "data" => [
                    "A_time" => "01:01:01",
                ]
            ],
            "Valid_A_time_2" => [
                "data" => [
                    "A_time" => "01:01:00",
                ]
            ],
            "Valid_A_time_3" => [
                "data" => [
                    "A_time" => "12:11:59",
                ]
            ],
            "Valid_A_time_4" => [
                "data" => [
                    "A_time" => "12:12:00",
                ]
            ],
            "Valid_B_time_1" => [
                "data" => [
                    "B_time" => "01:02",
                ]
            ],
            "Valid_B_time_2" => [
                "data" => [
                    "B_time" => "01:01",
                ]
            ],
            "Valid_B_time_3" => [
                "data" => [
                    "B_time" => "12:11",
                ]
            ],
            "Valid_B_time_4" => [
                "data" => [
                    "B_time" => "12:12",
                ]
            ],
            "Valid_C_time_1" => [
                "data" => [
                    "C_time" => "02",
                ]
            ],
            "Valid_C_time_2" => [
                "data" => [
                    "C_time" => "01",
                ]
            ],
            "Valid_C_time_3" => [
                "data" => [
                    "C_time" => "11",
                ]
            ],
            "Valid_C_time_4" => [
                "data" => [
                    "C_time" => "12",
                ]
            ],
            "Valid_D_time_1" => [
                "data" => [
                    "D_time" => "22:00:00",
                ]
            ],
            "Valid_D_time_2" => [
                "data" => [
                    "D_time" => "23:59:59",
                ]
            ],
            "Valid_D_time_3" => [
                "data" => [
                    "D_time" => "00:00:00",
                ]
            ],
            "Valid_D_time_4" => [
                "data" => [
                    "D_time" => "02:00:00",
                ]
            ],
            "Invalid_A_time_1" => [
                "data" => [
                    "A_time" => "01:01:60",
                ],
                "expected_msg" => ["A_time" => "A_time must be a valid time in format H:i:s"]
            ],
            "Invalid_A_time_2" => [
                "data" => [
                    "A_time" => "01:00:59",
                ],
                "expected_msg" => ["A_time" => "A_time time must be between 01:01:00 and 12:12:00"]
            ],
            "Invalid_A_time_3" => [
                "data" => [
                    "A_time" => "00:01:00",
                ],
                "expected_msg" => ["A_time" => "A_time time must be between 01:01:00 and 12:12:00"]
            ],
            "Invalid_A_time_4" => [
                "data" => [
                    "A_time" => "12:12:01",
                ],
                "expected_msg" => ["A_time" => "A_time time must be between 01:01:00 and 12:12:00"]
            ],
            "Invalid_A_time_5" => [
                "data" => [
                    "A_time" => "12:12:02",
                ],
                "expected_msg" => ["A_time" => "A_time time must be between 01:01:00 and 12:12:00"]
            ],
            "Invalid_B_time_1" => [
                "data" => [
                    "B_time" => "01:60",
                ],
                "expected_msg" => ["B_time" => "B_time must be a valid time in format i:s"]
            ],
            "Invalid_B_time_2" => [
                "data" => [
                    "B_time" => "01:00",
                ],
                "expected_msg" => ["B_time" => "B_time time must be between 01:01 and 12:12"]
            ],
            "Invalid_B_time_3" => [
                "data" => [
                    "B_time" => "00:59",
                ],
                "expected_msg" => ["B_time" => "B_time time must be between 01:01 and 12:12"]
            ],
            "Invalid_B_time_4" => [
                "data" => [
                    "B_time" => "12:13",
                ],
                "expected_msg" => ["B_time" => "B_time time must be between 01:01 and 12:12"]
            ],
            "Invalid_B_time_5" => [
                "data" => [
                    "B_time" => "13:12",
                ],
                "expected_msg" => ["B_time" => "B_time time must be between 01:01 and 12:12"]
            ],
            "Invalid_C_time_1" => [
                "data" => [
                    "C_time" => "60",
                ],
                "expected_msg" => ["C_time" => "C_time must be a valid time in format i"]
            ],
            "Invalid_C_time_2" => [
                "data" => [
                    "C_time" => "00",
                ],
                "expected_msg" => ["C_time" => "C_time time must be between 01 and 12"]
            ],
            "Invalid_C_time_3" => [
                "data" => [
                    "C_time" => "13",
                ],
                "expected_msg" => ["C_time" => "C_time time must be between 01 and 12"]
            ],
            "Invalid_C_time_4" => [
                "data" => [
                    "C_time" => "14",
                ],
                "expected_msg" => ["C_time" => "C_time time must be between 01 and 12"]
            ],
            "Exception_A_exc" => [
                "data" => [
                    "A_exc" => "01:01",
                ],
                "expected_msg" => '@field:A_exc, @method:time_between - Parameter 01:60 is not a valid time with format i:s'
            ],
            "Invalid_D_time_1" => [
                "data" => [
                    "D_time" => "21:00:00",
                ],
                "expected_msg" => ["D_time" => "D_time time must be between 22:00:00 and 02:00:00"]
            ],
            "Invalid_D_time_2" => [
                "data" => [
                    "D_time" => "03:00:00",
                ],
                "expected_msg" => ["D_time" => "D_time time must be between 22:00:00 and 02:00:00"]
            ],
            "Exception_B_exc" => [
                "data" => [
                    "B_exc" => "01:01",
                ],
                "expected_msg" => '@field:B_exc, @method:time_between - Parameter 60:00 is not a valid time with format H:i'
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
        ];

        return $method_info = [
            "rules" => $rules,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_datetime_between_by_relative_date_notation()
    {
        $rules = [
            "symbol" => [
                "A_datetime" => "optional|datetime>=<=[today, tomorrow]",
                "B_datetime" => "optional|datetime>=<=[-3 days, next week, Y-m-d H:i:s]",
                "C_datetime" => "optional|datetime>=<=[Tuesday, last day of next month, Y-m-d]",
                "A1_exc" => "optional|datetime>=<=[today1, tomorrow]",
                "A2_exc" => "optional|datetime>=<=[to-day, tomorrow]",
                "A3_exc" => "optional|datetime>=<=[todad, tomorrow]",
            ],
            "method" => [
                "A_datetime" => "optional|datetime_between[today, tomorrow]",
                "B_datetime" => "optional|datetime_between[-3 days, next week, Y-m-d H:i:s]",
                "C_datetime" => "optional|datetime_between[Tuesday, last day of next month, Y-m-d]",
                "A1_exc" => "optional|datetime_between[today1, tomorrow]",
                "A2_exc" => "optional|datetime_between[to-day, tomorrow]",
                "A3_exc" => "optional|datetime_between[todad, tomorrow]",
            ],
        ];

        // echo date('Y-m-d H:i:s', strtotime('next week')) . "\n";
        // echo date('Y-m-d H:i:s', strtotime('+7 days')) . "\n";die;

        $cases = [
            "Valid_A_datetime_1" => [
                "data" => [
                    "A_datetime" => date('Y-m-d'),
                ]
            ],
            "Valid_A_datetime_2" => [
                "data" => [
                    "A_datetime" => date('Y-m-d', strtotime('+1 day')),
                ]
            ],
            "Valid_B_datetime_1" => [
                "data" => [
                    "B_datetime" => date('Y-m-d H:i:s', strtotime('-3 days')),
                ]
            ],
            "Valid_B_datetime_2" => [
                "data" => [
                    "B_datetime" => date('Y-m-d H:i:s', strtotime('next week')),
                ]
            ],
            "Valid_C_datetime_1" => [
                "data" => [
                    "C_datetime" => date('Y-m-d', strtotime('Tuesday')),
                ]
            ],
            "Valid_C_datetime_2" => [
                "data" => [
                    "C_datetime" => date('Y-m-10', strtotime('next month')),
                ]
            ],

            /**
             * Invalid cases
             */
            "Invalid_A_datetime_1" => [
                "data" => [
                    "A_datetime" => date('Y-m-d', strtotime('-1 day')),
                ],
                "expected_msg" => ["A_datetime" => "A_datetime datetime must be between today and tomorrow"]
            ],
            "Invalid_A_datetime_2" => [
                "data" => [
                    "A_datetime" => date('Y-m-d', strtotime('+2 day')),
                ],
                "expected_msg" => ["A_datetime" => "A_datetime datetime must be between today and tomorrow"]
            ],
            "Invalid_B_datetime_1" => [
                "data" => [
                    "B_datetime" => date('Y-m-d H:i:s', strtotime('-4 day')),
                ],
                "expected_msg" => ["B_datetime" => "B_datetime datetime must be between -3 days and next week"]
            ],
            "Invalid_B_datetime_2" => [
                "data" => [
                    "B_datetime" => date('Y-m-d H:i:s', strtotime('+8 days')),
                ],
                "expected_msg" => ["B_datetime" => "B_datetime datetime must be between -3 days and next week"]
            ],
            "Invalid_C_datetime_1" => [
                "data" => [
                    "C_datetime" => date('Y-m-d', strtotime('-4 day')),
                ],
                "expected_msg" => ["C_datetime" => "C_datetime datetime must be between Tuesday and last day of next month"]
            ],
            "Invalid_C_datetime_2" => [
                "data" => [
                    "C_datetime" => date('Y-m-01', strtotime('+2 months')),
                ],
                "expected_msg" => ["C_datetime" => "C_datetime datetime must be between Tuesday and last day of next month"]
            ],

            /**
             * Exception cases
             */
            "Exception_A1_exc" => [
                "data" => [
                    "A1_exc" => "2024-05-01",
                ],
                "expected_msg" => '@field:A1_exc, @method:datetime_between - Parameter today1 is not a valid datetime'
            ],
            "Exception_A2_exc" => [
                "data" => [
                    "A2_exc" => "2024-05-01",
                ],
                "expected_msg" => '@field:A2_exc, @method:datetime_between - Parameter to-day is not a valid datetime'
            ],
            "Exception_A3_exc" => [
                "data" => [
                    "A3_exc" => "2024-05-01",
                ],
                "expected_msg" => '@field:A3_exc, @method:datetime_between - Parameter todad is not a valid datetime'
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
        ];

        return $method_info = [
            "rules" => $rules,
            "cases" => $cases,
            "extra" => $extra
        ];
    }

    protected function test_method_time_between_by_relative_date_notation()
    {
        $rules = [
            "symbol" => [
                "A_time" => "optional|time>=<=[last hour, +3 hours]",
                "B_time" => "optional|time>=<=[last hour, +3 hours, H:i:s]",
                "C_time" => "optional|time>=<=[last hour, +15 hours, H:i:s]",
                "A1_exc" => "optional|time>=<=[last hour1, +3 hours]",
                "A2_exc" => "optional|time>=<=[last hour, add 3 hours]",
            ],
            "method" => [
                "A_time" => "optional|time_between[last hour, +3 hours]",
                "B_time" => "optional|time_between[last hour, +3 hours, H:i:s]",
                "C_time" => "optional|time_between[last hour, +15 hours, H:i:s]",
                "A1_exc" => "optional|time_between[last hour1, +3 hours]",
                "A2_exc" => "optional|time_between[last hour, add 3 hours]",
            ],
        ];

        $cases = [
            "Valid_A_time_1" => [
                "data" => [
                    "A_time" => date('H:i:s'),
                ]
            ],
            "Valid_A_time_2" => [
                "data" => [
                    "A_time" => date('H:i:s', strtotime('+3 hours')),
                ]
            ],
            "Valid_B_time_1" => [
                "data" => [
                    "B_time" => date('H:i:s'),
                ]
            ],
            "Valid_B_time_2" => [
                "data" => [
                    "B_time" => date('H:i:s', strtotime('+3 hours')),
                ]
            ],
            "Valid_C_time_1" => [
                "data" => [
                    "C_time" => date('H:i:s', strtotime('-1 hour')),
                ]
            ],
            "Valid_C_time_2" => [
                "data" => [
                    "C_time" => date('H:i:s', strtotime('+10 hour')),
                ]
            ],
            "Valid_C_time_3" => [
                "data" => [
                    "C_time" => date('H:i:s', strtotime('+15 hour')),
                ]
            ],

            /**
             * Invalid cases
             */
            "Invalid_A_time_1" => [
                "data" => [
                    "A_time" => date('H:i:s', strtotime('-2 hours')),
                ],
                "expected_msg" => ["A_time" => "A_time time must be between last hour and +3 hours"]
            ],
            "Invalid_A_time_2" => [
                "data" => [
                    "A_time" => date('H:i:s', strtotime('-4 hours')),
                ],
                "expected_msg" => ["A_time" => "A_time time must be between last hour and +3 hours"]
            ],
            "Invalid_B_time_1" => [
                "data" => [
                    "B_time" => date('H:i:s', strtotime('-2 hours')),
                ],
                "expected_msg" => ["B_time" => "B_time time must be between last hour and +3 hours"]
            ],
            "Invalid_B_time_2" => [
                "data" => [
                    "B_time" => date('H:i:s', strtotime('-4 hours')),
                ],
                "expected_msg" => ["B_time" => "B_time time must be between last hour and +3 hours"]
            ],
            "Invalid_C_time_1" => [
                "data" => [
                    "C_time" => date('H:i:s', strtotime('-2 hours')),
                ],
                "expected_msg" => ["C_time" => "C_time time must be between last hour and +15 hours"]
            ],
            "Invalid_C_time_2" => [
                "data" => [
                    "C_time" => date('H:i:s', strtotime('+16 hours')),
                ],
                "expected_msg" => ["C_time" => "C_time time must be between last hour and +15 hours"]
            ],

            /**
             * Exception cases
             */
            "Exception_A1_exc" => [
                "data" => [
                    "A1_exc" => date('H:i:s'),
                ],
                "expected_msg" => '@field:A1_exc, @method:time_between - Notation last hour1 is invalid'
            ],
            "Exception_A2_exc" => [
                "data" => [
                    "A2_exc" => date('H:i:s'),
                ],
                "expected_msg" => '@field:A2_exc, @method:time_between - Notation add 3 hours is invalid'
            ],
        ];

        $extra = [
            "method_name" => __METHOD__,
        ];

        return $method_info = [
            "rules" => $rules,
            "cases" => $cases,
            "extra" => $extra
        ];
    }
}
