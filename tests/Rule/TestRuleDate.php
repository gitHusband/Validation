<?php

namespace githusband\Tests\Rule;

/**
 * The default timezone is 'Asia/Shanghai', {@see tests/Test.php}
 */
trait TestRuleDate
{
    protected function test_method_date()
    {
        $rules = [
            "symbol" => [
                "A_date" => "optional|date",
                "B_date" => "optional|date[Y-m-d]",
                "C_date" => "optional|date[Y/m/d]",
                "D_date" => "optional|date[Y/m-d]",
                "E_date" => "optional|date[d/m/Y]",
                "F_date" => "optional|date[Y-m-d H:i:s]",
                "G_date" => "optional|date[Y/m/d H:i:s]",
                "H_date" => "optional|date[YmdHis]",
                "I_date" => "optional|date[ATOM]",
                "J_date" => "optional|date[COOKIE]",
                "K_date" => "optional|date[RFC3339]",
                // "L_date" => "optional|date[RFC3339_EXTENDED]", // PHP 7+
                "M_date" => "optional|date[W3C]",
            ],
            "method" => [
                "A_date" => "optional|is_date",
                "B_date" => "optional|is_date[Y-m-d]",
                "C_date" => "optional|is_date[Y/m/d]",
                "D_date" => "optional|is_date[Y/m-d]",
                "E_date" => "optional|is_date[d/m/Y]",
                "F_date" => "optional|is_date[Y-m-d H:i:s]",
                "G_date" => "optional|is_date[Y/m/d H:i:s]",
                "H_date" => "optional|is_date[YmdHis]",
                "I_date" => "optional|is_date[ATOM]",
                "J_date" => "optional|is_date[COOKIE]",
                "K_date" => "optional|is_date[RFC3339]",
                // "L_date" => "optional|is_date[RFC3339_EXTENDED]", // PHP 7+
                "M_date" => "optional|is_date[W3C]",
            ],
        ];

        $cases = [
            "Valid_A_date_1" => [
                "data" => [
                    "A_date" => "20240423",
                ]
            ],
            "Valid_A_date_2" => [
                "data" => [
                    "A_date" => '2024-04-23',
                ]
            ],
            "Valid_A_date_3" => [
                "data" => [
                    "A_date" => '04/23/2024',
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
                    "F_date" => '2024-04-23 12:00:00',
                ]
            ],
            "Valid_G_date_1" => [
                "data" => [
                    "G_date" => '2024/04/23 12:00:00',
                ]
            ],
            "Valid_H_date_1" => [
                "data" => [
                    "H_date" => '20240423000000',
                ]
            ],
            "Valid_I_date_1" => [
                "data" => [
                    "I_date" => '2024-04-23T12:00:00+08:00',
                ]
            ],
            "Valid_J_date_1" => [
                "data" => [
                    "J_date" => 'Wed, 15-Apr-2024 12:00:00 UTC',
                ]
            ],
            "Valid_K_date_1" => [
                "data" => [
                    "K_date" => '2024-04-23T12:00:00+08:00',
                ]
            ],
            // "Valid_L_date_1" => [
            //     "data" => [
            //         "L_date" => '2024-04-23T12:00:00.000+08:00',
            //     ]
            // ],
            "Valid_M_date_1" => [
                "data" => [
                    "M_date" => '2024-04-23T12:00:00+08:00',
                ]
            ],

            /**
             * Invalid cases
             */
            "Invalid_A_date_1" => [
                "data" => [
                    "A_date" => "12345678",
                ],
                "expected_msg" => ["A_date" => "A_date must be a valid date"]
            ],
            "Invalid_A_date_2" => [
                "data" => [
                    "A_date" => "23/04/2024",
                ],
                "expected_msg" => ["A_date" => "A_date must be a valid date"]
            ],
            "Invalid_A_date_3" => [
                "data" => [
                    "A_date" => "2024-04-50",
                ],
                "expected_msg" => ["A_date" => "A_date must be a valid date"]
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
                    "F_date" => "2024-04-23 23:60:00",
                ],
                "expected_msg" => ["F_date" => "F_date must be a valid date in format Y-m-d H:i:s"]
            ],
            "Invalid_F_date_2" => [
                "data" => [
                    "F_date" => "2024-04-23+23:59:59",
                ],
                "expected_msg" => ["F_date" => "F_date must be a valid date in format Y-m-d H:i:s"]
            ],
            "Invalid_G_date_1" => [
                "data" => [
                    "G_date" => "2024/04/23 23:60:00",
                ],
                "expected_msg" => ["G_date" => "G_date must be a valid date in format Y/m/d H:i:s"]
            ],
            "Invalid_G_date_2" => [
                "data" => [
                    "G_date" => "2024/04/23+23:59:59",
                ],
                "expected_msg" => ["G_date" => "G_date must be a valid date in format Y/m/d H:i:s"]
            ],
            "Invalid_H_date_1" => [
                "data" => [
                    "H_date" => "20240423236000",
                ],
                "expected_msg" => ["H_date" => "H_date must be a valid date in format YmdHis"]
            ],
            "Invalid_H_date_2" => [
                "data" => [
                    "H_date" => "202404232359590",
                ],
                "expected_msg" => ["H_date" => "H_date must be a valid date in format YmdHis"]
            ],
            "Invalid_I_date_1" => [
                "data" => [
                    "I_date" => '2024-04-23T25:00:00+08:00',
                ],
                "expected_msg" => ["I_date" => "I_date must be a valid date in format ATOM"]
            ],
            "Invalid_I_date_2" => [
                "data" => [
                    "I_date" => '2024-04-23 23:00:00+08:00',
                ],
                "expected_msg" => ["I_date" => "I_date must be a valid date in format ATOM"]
            ],
            "Invalid_J_date_1" => [
                "data" => [
                    "J_date" => 'Wed 15-Apr-2024 15:52:01 UTC',
                ],
                "expected_msg" => ["J_date" => "J_date must be a valid date in format COOKIE"]
            ],
            "Invalid_J_date_2" => [
                "data" => [
                    "J_date" => 'Wed, 50-Apr-2024 15:52:01 UTC',
                ],
                "expected_msg" => ["J_date" => "J_date must be a valid date in format COOKIE"]
            ],
            "Invalid_K_date_1" => [
                "data" => [
                    "K_date" => '2024-04-23T25:00:00+08:00',
                ],
                "expected_msg" => ["K_date" => "K_date must be a valid date in format RFC3339"]
            ],
            "Invalid_K_date_2" => [
                "data" => [
                    "K_date" => '2024-04-23 23:00:00+08:00',
                ],
                "expected_msg" => ["K_date" => "K_date must be a valid date in format RFC3339"]
            ],
            // "Invalid_L_date_1" => [
            //     "data" => [
            //         "L_date" => '2024-04-23T25:00:00.000+08:00',
            //     ],
            //     "expected_msg" => ["L_date" => "L_date must be a valid date in format RFC3339_EXTENDED"]
            // ],
            // "Invalid_L_date_2" => [
            //     "data" => [
            //         "L_date" => '2024-04-23T23:00:00.0001+08:00',
            //     ],
            //     "expected_msg" => ["L_date" => "L_date must be a valid date in format RFC3339_EXTENDED"]
            // ],
            "Invalid_M_date_1" => [
                "data" => [
                    "M_date" => '2024-04-23T25:00:0008:00',
                ],
                "expected_msg" => ["M_date" => "M_date must be a valid date in format W3C"]
            ],
            "Invalid_M_date_2" => [
                "data" => [
                    "M_date" => '2024-04-23 23:00:00+08:00',
                ],
                "expected_msg" => ["M_date" => "M_date must be a valid date in format W3C"]
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
                "D_date" => "optional|date=[2024-04-23 01,Y-m-d H]",
                "E_date" => "optional|date=[2024-04-23 01:01,Y-m-d H:i]",
                "F_date" => "optional|date=[2024-04-23 01:01:01,Y-m-d H:i:s]",
                "G_date" => "optional|date=[2024-04-23 01:01,Y/m/d H:i]",
                "H_date" => "optional|date=[2024-04-23 01:01:01,Y/m/d H:i:s]",
                "I_date" => "optional|date=[2024-04-23T01:01:01+08:00,RFC3339]",
                "J_date" => "optional|date=[2024-04-23 01:01:01,RFC3339]",  // The default timezone is 'Asia/Shanghai', {@see tests/Test.php}
                "K_date" => "optional|date=[Tue\, 23-Apr-2024 01:01:01 Asia/Shanghai,COOKIE]",
                "L_date" => "optional|date=[2024-04-23 01:01:01,COOKIE]",   // The default timezone is 'Asia/Shanghai', {@see tests/Test.php}
                "A_exc" => "optional|date=[2024-04-50]",
                "C_exc" => "optional|date=[2024-13-23,d/Y/m]",
                "F_exc" => "optional|date=[2024-04-23 25:01:01,Y-m-d H:i:s]",
                "I_exc" => "optional|date=[2024-04-23T01:0101+08:00,RFC3339]",
                "K_exc" => "optional|date=[Tue\, 23-Apri-2024 01:01:01 Asia/Shanghai,COOKIE]",
            ],
            "method" => [
                "A_date" => "optional|date_equal[2024-04-23]",
                "B_date" => "optional|date_equal[2024-04-23,Y-m-d]",
                "C_date" => "optional|date_equal[2024-04-23,d/Y/m]",
                "D_date" => "optional|date_equal[2024-04-23 01,Y-m-d H]",
                "E_date" => "optional|date_equal[2024-04-23 01:01,Y-m-d H:i]",
                "F_date" => "optional|date_equal[2024-04-23 01:01:01,Y-m-d H:i:s]",
                "G_date" => "optional|date_equal[2024-04-23 01:01,Y/m/d H:i]",
                "H_date" => "optional|date_equal[2024-04-23 01:01:01,Y/m/d H:i:s]",
                "I_date" => "optional|date_equal[2024-04-23T01:01:01+08:00,RFC3339]",
                "J_date" => "optional|date_equal[2024-04-23 01:01:01,RFC3339]",  // The default timezone is 'Asia/Shanghai', {@see tests/Test.php}
                "K_date" => "optional|date_equal[Tue\, 23-Apr-2024 01:01:01 Asia/Shanghai,COOKIE]",
                "L_date" => "optional|date_equal[2024-04-23 01:01:01,COOKIE]",   // The default timezone is 'Asia/Shanghai', {@see tests/Test.php}
                "A_exc" => "optional|date_equal[2024-04-50]",
                "A_exc" => "optional|date_equal[2024-04-50]",
                "C_exc" => "optional|date_equal[2024-13-23,d/Y/m]",
                "F_exc" => "optional|date_equal[2024-04-23 25:01:01,Y-m-d H:i:s]",
                "I_exc" => "optional|date_equal[2024-04-23T01:0101+08:00,RFC3339]",
                "K_exc" => "optional|date_equal[Tue\, 23-Apri-2024 01:01:01 Asia/Shanghai,COOKIE]",
            ]
        ];

        $cases = [
            "Valid_A_date_1" => [
                "data" => [
                    "A_date" => "20240423",
                ]
            ],
            "Valid_A_date_2" => [
                "data" => [
                    "A_date" => '2024-04-23',
                ]
            ],
            "Valid_A_date_3" => [
                "data" => [
                    "A_date" => '04/23/2024',
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
                    "D_date" => '2024-04-23 01',
                ]
            ],
            "Valid_D_date_2" => [
                "data" => [
                    "D_date" => '2024-04-23 1',
                ]
            ],
            "Valid_E_date_1" => [
                "data" => [
                    "E_date" => '2024-04-23 01:01',
                ]
            ],
            "Valid_E_date_1" => [
                "data" => [
                    "E_date" => '2024-04-23 01:01',
                ]
            ],
            "Valid_F_date_1" => [
                "data" => [
                    "F_date" => '2024-04-23 01:01:01',
                ]
            ],
            "Valid_G_date_1" => [
                "data" => [
                    "G_date" => '2024/04/23 01:01',
                ]
            ],
            "Valid_H_date_1" => [
                "data" => [
                    "H_date" => '2024/04/23 01:01:01',
                ]
            ],
            "Valid_I_date_1" => [
                "data" => [
                    "I_date" => '2024-04-23T01:01:01+08:00',
                ]
            ],
            "Valid_I_date_2" => [
                "data" => [
                    "I_date" => '2024-04-22T17:01:01+00:00',
                ]
            ],
            "Valid_J_date_1" => [
                "data" => [
                    "J_date" => '2024-04-23T01:01:01+08:00',
                ]
            ],
            "Valid_J_date_2" => [
                "data" => [
                    "J_date" => '2024-04-22T17:01:01+00:00',
                ]
            ],
            "Valid_K_date_1" => [
                "data" => [
                    "K_date" => 'Tue, 23-Apr-2024 01:01:01 Asia/Shanghai',
                ]
            ],
            "Valid_K_date_2" => [
                "data" => [
                    "K_date" => 'Mon, 22-Apr-2024 17:01:01 UTC',
                ]
            ],
            "Valid_L_date_1" => [
                "data" => [
                    "L_date" => 'Tue, 23-Apr-2024 01:01:01 Asia/Shanghai',
                ]
            ],
            "Valid_L_date_2" => [
                "data" => [
                    "L_date" => 'Mon, 22-Apr-2024 17:01:01 UTC',
                ]
            ],

            /**
             * Invalid cases
             */
            "Invalid_A_date_1" => [
                "data" => [
                    "A_date" => "12345678",
                ],
                "expected_msg" => ["A_date" => "A_date must be a valid date"]
            ],
            "Invalid_A_date_2" => [
                "data" => [
                    "A_date" => "23/04/2024",
                ],
                "expected_msg" => ["A_date" => "A_date must be a valid date"]
            ],
            "Invalid_A_date_3" => [
                "data" => [
                    "A_date" => "2024-04-50",
                ],
                "expected_msg" => ["A_date" => "A_date must be a valid date"]
            ],
            "Invalid_A_date_4" => [
                "data" => [
                    "A_date" => "2024-05-01",
                ],
                "expected_msg" => ["A_date" => "A_date must be a valid date and equal to 2024-04-23"]
            ],
            "Invalid_A_date_5" => [
                "data" => [
                    "A_date" => "2024/05/01",
                ],
                "expected_msg" => ["A_date" => "A_date must be a valid date and equal to 2024-04-23"]
            ],
            "Invalid_A_date_6" => [
                "data" => [
                    "A_date" => "05/01/2024",
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
                    "D_date" => "2024-04-23 25",
                ],
                "expected_msg" => ["D_date" => "D_date must be a valid date in format Y-m-d H"]
            ],
            "Invalid_D_date_2" => [
                "data" => [
                    "D_date" => "2024-04-23 02",
                ],
                "expected_msg" => ["D_date" => "D_date must be a valid date and equal to 2024-04-23 01"]
            ],
            "Invalid_D_date_3" => [
                "data" => [
                    "D_date" => "2024-04-24 01",
                ],
                "expected_msg" => ["D_date" => "D_date must be a valid date and equal to 2024-04-23 01"]
            ],
            "Invalid_E_date_1" => [
                "data" => [
                    "E_date" => "2024-04-24 01",
                ],
                "expected_msg" => ["E_date" => "E_date must be a valid date in format Y-m-d H:i"]
            ],
            "Invalid_E_date_2" => [
                "data" => [
                    "E_date" => "2024-04-24 01:02",
                ],
                "expected_msg" => ["E_date" => "E_date must be a valid date and equal to 2024-04-23 01:01"]
            ],
            "Invalid_F_date_1" => [
                "data" => [
                    "F_date" => "2024-04-23 01:01",
                ],
                "expected_msg" => ["F_date" => "F_date must be a valid date in format Y-m-d H:i:s"]
            ],
            "Invalid_F_date_2" => [
                "data" => [
                    "F_date" => "2024-04-23 01:01:02",
                ],
                "expected_msg" => ["F_date" => "F_date must be a valid date and equal to 2024-04-23 01:01:01"]
            ],
            "Invalid_G_date_1" => [
                "data" => [
                    "G_date" => "2024/04/23 01:61",
                ],
                "expected_msg" => ["G_date" => "G_date must be a valid date in format Y/m/d H:i"]
            ],
            "Invalid_G_date_2" => [
                "data" => [
                    "G_date" => "2024/04/23 01:02",
                ],
                "expected_msg" => ["G_date" => "G_date must be a valid date and equal to 2024-04-23 01:01"]
            ],
            "Invalid_H_date_1" => [
                "data" => [
                    "H_date" => "2024/04/23 01:01:60",
                ],
                "expected_msg" => ["H_date" => "H_date must be a valid date in format Y/m/d H:i:s"]
            ],
            "Invalid_H_date_2" => [
                "data" => [
                    "H_date" => "2024/04/23 01:01:02",
                ],
                "expected_msg" => ["H_date" => "H_date must be a valid date and equal to 2024-04-23 01:01:01"]
            ],
            "Invalid_I_date_1" => [
                "data" => [
                    "I_date" => '2024-04-23T01:01:01&08:00',
                ],
                "expected_msg" => ["I_date" => "I_date must be a valid date in format RFC3339"]
            ],
            "Invalid_I_date_2" => [
                "data" => [
                    "I_date" => '2024-04-23T01:01:02+08:00',
                ],
                "expected_msg" => ["I_date" => "I_date must be a valid date and equal to 2024-04-23T01:01:01+08:00"]
            ],
            "Invalid_I_date_3" => [
                "data" => [
                    "I_date" => '2024-04-23T01:01:01+00:00',
                ],
                "expected_msg" => ["I_date" => "I_date must be a valid date and equal to 2024-04-23T01:01:01+08:00"]
            ],
            "Invalid_J_date_1" => [
                "data" => [
                    "J_date" => '2024-04-23T01:01:01&08:00',
                ],
                "expected_msg" => ["J_date" => "J_date must be a valid date in format RFC3339"]
            ],
            "Invalid_J_date_2" => [
                "data" => [
                    "J_date" => '2024-04-23T01:01:02+08:00',
                ],
                "expected_msg" => ["J_date" => "J_date must be a valid date and equal to 2024-04-23 01:01:01"]
            ],
            "Invalid_J_date_3" => [
                "data" => [
                    "J_date" => '2024-04-23T01:01:01+00:00',
                ],
                "expected_msg" => ["J_date" => "J_date must be a valid date and equal to 2024-04-23 01:01:01"]
            ],
            "Invalid_K_date_1" => [
                "data" => [
                    "K_date" => 'Tue. 23-Apr-2024 01:01:01 Asia/Shanghai',
                ],
                "expected_msg" => ["K_date" => "K_date must be a valid date in format COOKIE"]
            ],
            "Invalid_K_date_2" => [
                "data" => [
                    "K_date" => 'Tue, 23-Apr-2024 01:01:02 Asia/Shanghai',
                ],
                "expected_msg" => ["K_date" => "K_date must be a valid date and equal to Tue, 23-Apr-2024 01:01:01 Asia/Shanghai"]
            ],
            "Invalid_K_date_3" => [
                "data" => [
                    "K_date" => 'Tue, 23-Apr-2024 01:01:01 UTC',
                ],
                "expected_msg" => ["K_date" => "K_date must be a valid date and equal to Tue, 23-Apr-2024 01:01:01 Asia/Shanghai"]
            ],
            "Invalid_L_date_1" => [
                "data" => [
                    "L_date" => 'Tue. 23-Apr-2024 01:01:01 Asia/Shanghai',
                ],
                "expected_msg" => ["L_date" => "L_date must be a valid date in format COOKIE"]
            ],
            "Invalid_L_date_2" => [
                "data" => [
                    "L_date" => 'Tue, 23-Apr-2024 01:01:02 Asia/Shanghai',
                ],
                "expected_msg" => ["L_date" => "L_date must be a valid date and equal to 2024-04-23 01:01:01"]
            ],
            "Invalid_L_date_3" => [
                "data" => [
                    "L_date" => 'Tue, 23-Apr-2024 01:01:01 UTC',
                ],
                "expected_msg" => ["L_date" => "L_date must be a valid date and equal to 2024-04-23 01:01:01"]
            ],
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
            "Exception_F_exc" => [
                "data" => [
                    "F_exc" => "2024-04-23 01:01:01",
                ],
                "expected_msg" => '@field:F_exc, @method:date_equal - Parameter 2024-04-23 25:01:01 is not a valid date'
            ],
            "Exception_I_exc" => [
                "data" => [
                    "I_exc" => "2024-04-23T01:01:01+08:00",
                ],
                "expected_msg" => '@field:I_exc, @method:date_equal - Parameter 2024-04-23T01:0101+08:00 is not a valid date'
            ],
            "Exception_K_exc" => [
                "data" => [
                    "K_exc" => "Tue, 23-Apr-2024 01:01:01 Asia/Shanghai",
                ],
                "expected_msg" => '@field:K_exc, @method:date_equal - Parameter Tue, 23-Apri-2024 01:01:01 Asia/Shanghai is not a valid date'
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
                "A_date" => "optional|date>=<=[2024-04-23, 2024-05-01]",
                "B_date" => "optional|date>=<=[2024-04-23, 2024-05-01, Y-m-d]",
                "C_date" => "optional|date>=<=[2024-04-23, 2024-05-01, m/d/Y]",
                "D_date" => "optional|date>=<=[2024-04-23 01, 2024-05-01 12, Y-m-d H]",
                "E_date" => "optional|date>=<=[2024-04-23 01:01, 2024-05-01 12:12, Y-m-d H:i]",
                "F_date" => "optional|date>=<=[2024-04-23 01:01:01, 2024-05-01 12:12:12, Y-m-d H:i:s]",

                "I_date" => "optional|date>=<=[2024-04-23T01:01:01+08:00, 2024-05-01T12:12:12+08:00, RFC3339]",
                "J_date" => "optional|date>=<=[2024-04-23 01:01:01, 2024-05-01 12:12:12, RFC3339]",   // The default timezone is 'Asia/Shanghai', {@see tests/Test.php}
                "K_date" => "optional|date>=<=[Tue\, 23 Apr 2024 01:01:01 +0800, Wed\, 01 May 2024 12:12:12 +0800, RFC2822]",
                "L_date" => "optional|date>=<=[2024-04-23 01:01:01, 2024-05-01 12:12:12, RFC2822]",   // The default timezone is 'Asia/Shanghai', {@see tests/Test.php}
                "A1_exc" => "optional|date>=<=[2024-04-50, 2024-05-01]",
                "A2_exc" => "optional|date>=<=[2024-04-23, 2024-13-01]",
                "E1_exc" => "optional|date>=<=[2024-04-23 01:60, 2024-05-01 12:12, Y-m-d H:i]",
                "E2_exc" => "optional|date>=<=[2024-04-23 01:01, 2024-05-50 12:12, Y-m-d H:i]",
                "K1_exc" => "optional|date>=<=[Tue\, 23 Apri 2024 01:01:01 +0800, Wed\, 01 May 2024 12:12:12 +0800, RFC2822]",
                "K2_exc" => "optional|date>=<=[Tue\, 23 Apr 2024 01:01:01 +0800, Wen\, 01 May 2024 12:12:12 +0800, RFC2822]",
            ],
            "method" => [
                "A_date" => "optional|date_between[2024-04-23, 2024-05-01]",
                "B_date" => "optional|date_between[2024-04-23, 2024-05-01, Y-m-d]",
                "C_date" => "optional|date_between[2024-04-23, 2024-05-01, m/d/Y]",
                "D_date" => "optional|date_between[2024-04-23 01, 2024-05-01 12, Y-m-d H]",
                "E_date" => "optional|date_between[2024-04-23 01:01, 2024-05-01 12:12, Y-m-d H:i]",
                "F_date" => "optional|date_between[2024-04-23 01:01:01, 2024-05-01 12:12:12, Y-m-d H:i:s]",

                "I_date" => "optional|date_between[2024-04-23T01:01:01+08:00, 2024-05-01T12:12:12+08:00, RFC3339]",
                "J_date" => "optional|date_between[2024-04-23 01:01:01, 2024-05-01 12:12:12, RFC3339]",   // The default timezone is 'Asia/Shanghai', {@see tests/Test.php}
                "K_date" => "optional|date_between[Tue\, 23 Apr 2024 01:01:01 +0800, Wed\, 01 May 2024 12:12:12 +0800, RFC2822]",
                "L_date" => "optional|date_between[2024-04-23 01:01:01, 2024-05-01 12:12:12, RFC2822]",   // The default timezone is 'Asia/Shanghai', {@see tests/Test.php}
                "A1_exc" => "optional|date_between[2024-04-50, 2024-05-01]",
                "A2_exc" => "optional|date_between[2024-04-23, 2024-13-01]",
                "E1_exc" => "optional|date_between[2024-04-23 01:60, 2024-05-01 12:12, Y-m-d H:i]",
                "E2_exc" => "optional|date_between[2024-04-23 01:01, 2024-05-50 12:12, Y-m-d H:i]",
                "K1_exc" => "optional|date_between[Tue\, 23 Apri 2024 01:01:01 +0800, Wed\, 01 May 2024 12:12:12 +0800, RFC2822]",
                "K2_exc" => "optional|date_between[Tue\, 23 Apr 2024 01:01:01 +0800, Wen\, 01 May 2024 12:12:12 +0800, RFC2822]",
            ],
        ];

        $cases = [
            "Valid_A_date_1" => [
                "data" => [
                    "A_date" => "20240424",
                ]
            ],
            "Valid_A_date_2" => [
                "data" => [
                    "A_date" => '2024-04-24',
                ]
            ],
            "Valid_A_date_3" => [
                "data" => [
                    "A_date" => '04/24/2024',
                ]
            ],
            "Valid_A_date_4" => [
                "data" => [
                    "A_date" => '2024-04-23 00:00',
                ]
            ],
            "Valid_A_date_5" => [
                "data" => [
                    "A_date" => '2024/04/23 00:00:00',
                ]
            ],
            "Valid_A_date_6" => [
                "data" => [
                    "A_date" => '2024-05-01 00:00:00',
                ]
            ],
            "Valid_B_date_1" => [
                "data" => [
                    "B_date" => '2024-04-23',
                ]
            ],
            "Valid_B_date_2" => [
                "data" => [
                    "B_date" => '2024-05-01',
                ]
            ],
            "Valid_B_date_3" => [
                "data" => [
                    "B_date" => '2024-04-25',
                ]
            ],
            "Valid_C_date_1" => [
                "data" => [
                    "C_date" => '04/23/2024',
                ]
            ],
            "Valid_C_date_2" => [
                "data" => [
                    "C_date" => '05/01/2024',
                ]
            ],
            "Valid_C_date_3" => [
                "data" => [
                    "C_date" => '04/25/2024',
                ]
            ],
            "Valid_D_date_1" => [
                "data" => [
                    "D_date" => '2024-04-23 01',
                ]
            ],
            "Valid_D_date_2" => [
                "data" => [
                    "D_date" => '2024-05-01 12',
                ]
            ],
            "Valid_D_date_3" => [
                "data" => [
                    "D_date" => '2024-05-01 08',
                ]
            ],
            "Valid_E_date_1" => [
                "data" => [
                    "E_date" => '2024-04-23 01:01',
                ]
            ],
            "Valid_E_date_2" => [
                "data" => [
                    "E_date" => '2024-05-01 12:12',
                ]
            ],
            "Valid_E_date_3" => [
                "data" => [
                    "E_date" => '2024-05-01 12:08',
                ]
            ],
            "Valid_F_date_1" => [
                "data" => [
                    "F_date" => '2024-04-23 01:01:01',
                ]
            ],
            "Valid_F_date_2" => [
                "data" => [
                    "F_date" => '2024-05-01 12:12:12',
                ]
            ],
            "Valid_F_date_3" => [
                "data" => [
                    "F_date" => '2024-05-01 12:12:08',
                ]
            ],
            "Valid_I_date_1" => [
                "data" => [
                    "I_date" => '2024-04-23T01:01:01+08:00',
                ]
            ],
            "Valid_I_date_2" => [
                "data" => [
                    "I_date" => '2024-05-01T12:12:12+08:00',
                ]
            ],
            "Valid_I_date_3" => [
                "data" => [
                    "I_date" => '2024-05-01T12:12:08+08:00',
                ]
            ],
            "Valid_I_date_4" => [
                "data" => [
                    "I_date" => '2024-04-22T17:01:01+00:00',
                ]
            ],
            "Valid_I_date_5" => [
                "data" => [
                    "I_date" => '2024-05-01T14:12:12+10:00',
                ]
            ],
            "Valid_J_date_1" => [
                "data" => [
                    "J_date" => '2024-04-23T01:01:01+08:00',
                ]
            ],
            "Valid_J_date_2" => [
                "data" => [
                    "J_date" => '2024-05-01T12:12:12+08:00',
                ]
            ],
            "Valid_J_date_3" => [
                "data" => [
                    "J_date" => '2024-05-01T12:12:08+08:00',
                ]
            ],
            "Valid_J_date_4" => [
                "data" => [
                    "J_date" => '2024-04-22T17:01:01+00:00',
                ]
            ],
            "Valid_J_date_5" => [
                "data" => [
                    "J_date" => '2024-05-01T14:12:12+10:00',
                ]
            ],
            "Valid_K_date_1" => [
                "data" => [
                    "K_date" => 'Tue, 23 Apr 2024 01:01:01 +0800',
                ]
            ],
            "Valid_K_date_2" => [
                "data" => [
                    "K_date" => 'Wed, 01 May 2024 12:12:12 +0800',
                ]
            ],
            "Valid_K_date_3" => [
                "data" => [
                    "K_date" => 'Wed, 01 May 2024 12:12:08 +0800',
                ]
            ],
            "Valid_K_date_4" => [
                "data" => [
                    "K_date" => 'Mon, 22 Apr 2024 17:01:01 +0000',
                ]
            ],
            "Valid_K_date_5" => [
                "data" => [
                    "K_date" => 'Wed, 01 May 2024 16:12:12 +1200',
                ]
            ],
            "Valid_L_date_1" => [
                "data" => [
                    "L_date" => 'Tue, 23 Apr 2024 01:01:01 +0800',
                ]
            ],
            "Valid_L_date_2" => [
                "data" => [
                    "L_date" => 'Wed, 01 May 2024 12:12:12 +0800',
                ]
            ],
            "Valid_L_date_3" => [
                "data" => [
                    "L_date" => 'Wed, 01 May 2024 12:12:08 +0800',
                ]
            ],
            "Valid_L_date_4" => [
                "data" => [
                    "L_date" => 'Mon, 22 Apr 2024 17:01:01 +0000',
                ]
            ],
            "Valid_L_date_5" => [
                "data" => [
                    "L_date" => 'Wed, 01 May 2024 16:12:12 +1200',
                ]
            ],

            /**
             * Invalid cases
             */
            "Invalid_A_date_1" => [
                "data" => [
                    "A_date" => "12345678",
                ],
                "expected_msg" => ["A_date" => "A_date must be a valid date"]
            ],
            "Invalid_A_date_2" => [
                "data" => [
                    "A_date" => "2024-04-50",
                ],
                "expected_msg" => ["A_date" => "A_date must be a valid date"]
            ],
            "Invalid_A_date_3" => [
                "data" => [
                    "A_date" => "2024-04-22",
                ],
                "expected_msg" => ["A_date" => "A_date date must be between 2024-04-23 and 2024-05-01"]
            ],
            "Invalid_A_date_4" => [
                "data" => [
                    "A_date" => "2024-05-02",
                ],
                "expected_msg" => ["A_date" => "A_date date must be between 2024-04-23 and 2024-05-01"]
            ],
            "Invalid_A_date_5" => [
                "data" => [
                    "A_date" => "2024-05-01 00:00:01",
                ],
                "expected_msg" => ["A_date" => "A_date date must be between 2024-04-23 and 2024-05-01"]
            ],
            "Invalid_B_date_1" => [
                "data" => [
                    "B_date" => "2024/04/23",
                ],
                "expected_msg" => ["B_date" => "B_date must be a valid date in format Y-m-d"]
            ],
            "Invalid_B_date_2" => [
                "data" => [
                    "B_date" => "2024-04-50",
                ],
                "expected_msg" => ["B_date" => "B_date must be a valid date in format Y-m-d"]
            ],
            "Invalid_B_date_3" => [
                "data" => [
                    "B_date" => "2024-04-22",
                ],
                "expected_msg" => ["B_date" => "B_date date must be between 2024-04-23 and 2024-05-01"]
            ],
            "Invalid_B_date_4" => [
                "data" => [
                    "B_date" => "2024-05-02",
                ],
                "expected_msg" => ["B_date" => "B_date date must be between 2024-04-23 and 2024-05-01"]
            ],
            "Invalid_C_date_1" => [
                "data" => [
                    "C_date" => "2024/04/23",
                ],
                "expected_msg" => ["C_date" => "C_date must be a valid date in format m/d/Y"]
            ],
            "Invalid_C_date_2" => [
                "data" => [
                    "C_date" => "04/50/2024",
                ],
                "expected_msg" => ["C_date" => "C_date must be a valid date in format m/d/Y"]
            ],
            "Invalid_C_date_3" => [
                "data" => [
                    "C_date" => "04/22/2024",
                ],
                "expected_msg" => ["C_date" => "C_date date must be between 2024-04-23 and 2024-05-01"]
            ],
            "Invalid_C_date_4" => [
                "data" => [
                    "C_date" => "05/02/2024",
                ],
                "expected_msg" => ["C_date" => "C_date date must be between 2024-04-23 and 2024-05-01"]
            ],
            "Invalid_D_date_1" => [
                "data" => [
                    "D_date" => "2024/04/23 01",
                ],
                "expected_msg" => ["D_date" => "D_date must be a valid date in format Y-m-d H"]
            ],
            "Invalid_D_date_2" => [
                "data" => [
                    "D_date" => "2024-04-23 24",
                ],
                "expected_msg" => ["D_date" => "D_date must be a valid date in format Y-m-d H"]
            ],
            "Invalid_D_date_3" => [
                "data" => [
                    "D_date" => "2024-04-23 00",
                ],
                "expected_msg" => ["D_date" => "D_date date must be between 2024-04-23 01 and 2024-05-01 12"]
            ],
            "Invalid_D_date_4" => [
                "data" => [
                    "D_date" => "2024-05-01 13",
                ],
                "expected_msg" => ["D_date" => "D_date date must be between 2024-04-23 01 and 2024-05-01 12"]
            ],
            "Invalid_E_date_1" => [
                "data" => [
                    "E_date" => "2024-04-23 01_01",
                ],
                "expected_msg" => ["E_date" => "E_date must be a valid date in format Y-m-d H:i"]
            ],
            "Invalid_E_date_2" => [
                "data" => [
                    "E_date" => "2024-04-23 01:60",
                ],
                "expected_msg" => ["E_date" => "E_date must be a valid date in format Y-m-d H:i"]
            ],
            "Invalid_E_date_3" => [
                "data" => [
                    "E_date" => "2024-04-23 01:00",
                ],
                "expected_msg" => ["E_date" => "E_date date must be between 2024-04-23 01:01 and 2024-05-01 12:12"]
            ],
            "Invalid_E_date_4" => [
                "data" => [
                    "E_date" => "2024-05-01 12:13",
                ],
                "expected_msg" => ["E_date" => "E_date date must be between 2024-04-23 01:01 and 2024-05-01 12:12"]
            ],
            "Invalid_F_date_1" => [
                "data" => [
                    "F_date" => "2024-04-23 01_01_01",
                ],
                "expected_msg" => ["F_date" => "F_date must be a valid date in format Y-m-d H:i:s"]
            ],
            "Invalid_F_date_2" => [
                "data" => [
                    "F_date" => "2024-04-23 01:01:60",
                ],
                "expected_msg" => ["F_date" => "F_date must be a valid date in format Y-m-d H:i:s"]
            ],
            "Invalid_F_date_3" => [
                "data" => [
                    "F_date" => "2024-04-23 01:01:00",
                ],
                "expected_msg" => ["F_date" => "F_date date must be between 2024-04-23 01:01:01 and 2024-05-01 12:12:12"]
            ],
            "Invalid_F_date_4" => [
                "data" => [
                    "F_date" => "2024-05-01 12:12:13",
                ],
                "expected_msg" => ["F_date" => "F_date date must be between 2024-04-23 01:01:01 and 2024-05-01 12:12:12"]
            ],
            "Invalid_I_date_1" => [
                "data" => [
                    "I_date" => "2024-04-23T01:01:01@08:00",
                ],
                "expected_msg" => ["I_date" => "I_date must be a valid date in format RFC3339"]
            ],
            "Invalid_I_date_2" => [
                "data" => [
                    "I_date" => "2024-04-23T24:01:01+08:00",
                ],
                "expected_msg" => ["I_date" => "I_date must be a valid date in format RFC3339"]
            ],
            "Invalid_I_date_3" => [
                "data" => [
                    "I_date" => "2024-04-23T01:01:00+08:00",
                ],
                "expected_msg" => ["I_date" => "I_date date must be between 2024-04-23T01:01:01+08:00 and 2024-05-01T12:12:12+08:00"]
            ],
            "Invalid_I_date_4" => [
                "data" => [
                    "I_date" => "2024-05-01T12:12:13+08:00",
                ],
                "expected_msg" => ["I_date" => "I_date date must be between 2024-04-23T01:01:01+08:00 and 2024-05-01T12:12:12+08:00"]
            ],
            "Invalid_I_date_5" => [
                "data" => [
                    "I_date" => '2024-04-22T17:01:00+00:00',
                ],
                "expected_msg" => ["I_date" => "I_date date must be between 2024-04-23T01:01:01+08:00 and 2024-05-01T12:12:12+08:00"]
            ],
            "Invalid_I_date_6" => [
                "data" => [
                    "I_date" => '2024-05-01T14:12:13+10:00',
                ],
                "expected_msg" => ["I_date" => "I_date date must be between 2024-04-23T01:01:01+08:00 and 2024-05-01T12:12:12+08:00"]
            ],
            "Invalid_J_date_1" => [
                "data" => [
                    "J_date" => "2024-04-23T01:01:01@08:00",
                ],
                "expected_msg" => ["J_date" => "J_date must be a valid date in format RFC3339"]
            ],
            "Invalid_J_date_2" => [
                "data" => [
                    "J_date" => "2024-04-23T24:01:01+08:00",
                ],
                "expected_msg" => ["J_date" => "J_date must be a valid date in format RFC3339"]
            ],
            "Invalid_J_date_3" => [
                "data" => [
                    "J_date" => "2024-04-23T01:01:00+08:00",
                ],
                "expected_msg" => ["J_date" => "J_date date must be between 2024-04-23 01:01:01 and 2024-05-01 12:12:12"]
            ],
            "Invalid_J_date_4" => [
                "data" => [
                    "J_date" => "2024-05-01T12:12:13+08:00",
                ],
                "expected_msg" => ["J_date" => "J_date date must be between 2024-04-23 01:01:01 and 2024-05-01 12:12:12"]
            ],
            "Invalid_J_date_5" => [
                "data" => [
                    "J_date" => '2024-04-22T17:01:00+00:00',
                ],
                "expected_msg" => ["J_date" => "J_date date must be between 2024-04-23 01:01:01 and 2024-05-01 12:12:12"]
            ],
            "Invalid_J_date_6" => [
                "data" => [
                    "J_date" => '2024-05-01T14:12:13+10:00',
                ],
                "expected_msg" => ["J_date" => "J_date date must be between 2024-04-23 01:01:01 and 2024-05-01 12:12:12"]
            ],
            "Invalid_K_date_1" => [
                "data" => [
                    "K_date" => "Tue, 23 Apr 2024 01:01:01 #0800",
                ],
                "expected_msg" => ["K_date" => "K_date must be a valid date in format RFC2822"]
            ],
            "Invalid_K_date_2" => [
                "data" => [
                    "K_date" => "Tue, 23 Apr 2024 01:60:01 +0800",
                ],
                "expected_msg" => ["K_date" => "K_date must be a valid date in format RFC2822"]
            ],
            "Invalid_K_date_3" => [
                "data" => [
                    "K_date" => "Tue, 23 Apr 2024 01:01:00 +0800",
                ],
                "expected_msg" => ["K_date" => "K_date date must be between Tue, 23 Apr 2024 01:01:01 +0800 and Wed, 01 May 2024 12:12:12 +0800"]
            ],
            "Invalid_K_date_4" => [
                "data" => [
                    "K_date" => "Wed, 01 May 2024 12:12:13 +0800",
                ],
                "expected_msg" => ["K_date" => "K_date date must be between Tue, 23 Apr 2024 01:01:01 +0800 and Wed, 01 May 2024 12:12:12 +0800"]
            ],
            "Invalid_K_date_5" => [
                "data" => [
                    "K_date" => 'Mon, 22 Apr 2024 17:01:00 +0000',
                ],
                "expected_msg" => ["K_date" => "K_date date must be between Tue, 23 Apr 2024 01:01:01 +0800 and Wed, 01 May 2024 12:12:12 +0800"]
            ],
            "Invalid_K_date_6" => [
                "data" => [
                    "K_date" => 'Wed, 01 May 2024 16:12:13 +1200',
                ],
                "expected_msg" => ["K_date" => "K_date date must be between Tue, 23 Apr 2024 01:01:01 +0800 and Wed, 01 May 2024 12:12:12 +0800"]
            ],
            "Invalid_L_date_1" => [
                "data" => [
                    "L_date" => "Tue, 23 Apr 2024 01:01:01 #0800",
                ],
                "expected_msg" => ["L_date" => "L_date must be a valid date in format RFC2822"]
            ],
            "Invalid_L_date_2" => [
                "data" => [
                    "L_date" => "Tue, 23 Apr 2024 01:60:01 +0800",
                ],
                "expected_msg" => ["L_date" => "L_date must be a valid date in format RFC2822"]
            ],
            "Invalid_L_date_3" => [
                "data" => [
                    "L_date" => "Tue, 23 Apr 2024 01:01:00 +0800",
                ],
                "expected_msg" => ["L_date" => "L_date date must be between 2024-04-23 01:01:01 and 2024-05-01 12:12:12"]
            ],
            "Invalid_L_date_4" => [
                "data" => [
                    "L_date" => "Wed, 01 May 2024 12:12:13 +0800",
                ],
                "expected_msg" => ["L_date" => "L_date date must be between 2024-04-23 01:01:01 and 2024-05-01 12:12:12"]
            ],
            "Invalid_L_date_5" => [
                "data" => [
                    "L_date" => 'Mon, 22 Apr 2024 17:01:00 +0000',
                ],
                "expected_msg" => ["L_date" => "L_date date must be between 2024-04-23 01:01:01 and 2024-05-01 12:12:12"]
            ],
            "Invalid_L_date_6" => [
                "data" => [
                    "L_date" => 'Wed, 01 May 2024 16:12:13 +1200',
                ],
                "expected_msg" => ["L_date" => "L_date date must be between 2024-04-23 01:01:01 and 2024-05-01 12:12:12"]
            ],
            "Exception_A1_exc" => [
                "data" => [
                    "A1_exc" => "2024-05-01",
                ],
                "expected_msg" => '@field:A1_exc, @method:date_between - Parameter 2024-04-50 is not a valid date'
            ],
            "Exception_A2_exc" => [
                "data" => [
                    "A2_exc" => "2024-05-01",
                ],
                "expected_msg" => '@field:A2_exc, @method:date_between - Parameter 2024-13-01 is not a valid date'
            ],
            "Exception_E1_exc" => [
                "data" => [
                    "E1_exc" => "2024-05-01 12:12",
                ],
                "expected_msg" => '@field:E1_exc, @method:date_between - Parameter 2024-04-23 01:60 is not a valid date'
            ],
            "Exception_E2_exc" => [
                "data" => [
                    "E2_exc" => "2024-05-01 12:12",
                ],
                "expected_msg" => '@field:E2_exc, @method:date_between - Parameter 2024-05-50 12:12 is not a valid date'
            ],
            "Exception_K1_exc" => [
                "data" => [
                    "K1_exc" => "Wed, 01 May 2024 12:12:12 +0800",
                ],
                "expected_msg" => '@field:K1_exc, @method:date_between - Parameter Tue, 23 Apri 2024 01:01:01 +0800 is not a valid date'
            ],
            "Exception_K2_exc" => [
                "data" => [
                    "K2_exc" => "Wed, 01 May 2024 12:12:12 +0800",
                ],
                "expected_msg" => '@field:K2_exc, @method:date_between - Parameter Wen, 01 May 2024 12:12:12 +0800 is not a valid date'
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
