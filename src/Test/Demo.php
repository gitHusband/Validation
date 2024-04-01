<?php

namespace githusband\Test;

use githusband\Validation;
use githusband\Test\TestCommon;

class Demo extends TestCommon
{
    public function __construct()
    {
    }

    public function success($data = array())
    {
        if (empty($data)) {
            $data = [
                "id" => 12,
                "name" => "LinJunjie",
                "gender" => "female",
                "dob" => "2000-01-01",
                "age" => 30,
                "height" => 165,
                "height_unit" => "cm",
                "weight" => 80,
                "weight_unit" => "kg",
                "email" => "10000@qq.com",
                "phone" => "15620004000",
                "ip" => "192.168.1.1",
                "mac" => "06:19:C2:FA:36:2B",
                "education" => [
                    "primary_school" => "Qiankeng Xiaoxue",
                    "junior_middle_school" => "Qiankeng Zhongxue",
                    "high_school" => "Mianhu Gaozhong",
                    "university" => "Foshan University",
                ],
                "company" => [
                    "name" => "Qiankeng Company",
                    "website" => "https://www.qiankeng.com",
                    "country" => "China",
                    "addr" => "Foshan Nanhai Guicheng",
                    "postcode" => "532000",
                    "colleagues" => [
                        [
                            "name" => "Judy",
                            "position" => "Reception"
                        ],
                        [
                            "name" => "Sian",
                            "position" => "Financial"
                        ],
                        [
                            "name" => "Brook",
                            "position" => "JAVA"
                        ],
                        [
                            "name" => "Kurt",
                            "position" => "PHP"
                        ],
                    ],
                    "boss" => [
                        "Mike",
                        "David",
                        "Johnny",
                        "Extra",
                    ]
                ]
            ];
        }

        $rule = [
            "id" => "required|/^\d+$/",
            "name" => "required|string|len<=>[8,32]",
            "gender" => "required|(s)[male,female]",
            "dob" => "required|dob",
            "age" => "required|check_age[@gender,30] >> @this is wrong",
            "height[or]" => [
                "required|=(@height_unit,cm)|<=>=[100,200] >> @this should be in [100,200] when height_unit is cm",
                "required|=(@height_unit,m)|<=>=[1,2] >> @this should be in [1,2] when height_unit is m",
            ],
            "height_unit" => "required|(s)[cm,m]",
            "weight[or]" => [
                "required|=(@weight_unit,kg)|<=>=[40,100]",
                "required|=(@weight_unit,lb)|<=>[88,220]",
            ],
            "weight_unit" => "required|(s)[kg,lb]",
            "email" => "required|email",
            "phone" => "required|/(^1[3|4|5|6|7|8|9]\d{9}$)|(^09\d{8}$)/ >> phone number error",
            "ip" => "optional|ip",
            "mac" => "optional|mac",
            "education" => [
                "primary_school" => "required|=[Qiankeng Xiaoxue]",
                "junior_middle_school" => "required|!=[Foshan Zhongxue]",
                "high_school" => "if(=(@junior_middle_school,Mianhu Zhongxue))|required|len>[8]",
                "university" => "!if(=(@junior_middle_school,Mianhu Zhongxue))|required|len>[8]",
            ],
            "company" => [
                "name" => "required|len<=>[8,64]",
                "website" => "required|url",
                "country" => "optional|len<=[32]",
                "addr" => "required|len>[16]",
                "postcode" => "optional|len<[16]|check_postcode(@parent)",
                "colleagues.*" => [
                    "name" => "required|string|len<=>[3,32]",
                    "position" => "required|(s)[Reception,Financial,PHP,JAVA]"
                ],
                "boss" => [
                    "required|=[Mike]",
                    "required|(s)[Johnny,David]",
                    "optional|(s)[Johnny,David]"
                ]
            ],
            "favourite_food[optional].*" => [
                "name" => "required|string",
                "place_name" => "optional|string"
            ]
        ];

        $validation_conf = [
            'language' => 'en-us',
            'validation_global' => true,
        ];

        return $this->validate($data, $rule, $validation_conf);
    }

    public function success2($data = array())
    {
        if (empty($data)) {
            $data = [
                "id" => 12,
                "name" => "LinJunjie",
                "gender" => "female",
                "dob" => "2000-01-01",
                "age" => 30,
                "height" => 165,
                "height_unit" => "cm",
                "weight" => 80,
                "weight_unit" => "kg",
                "email" => "10000@qq.com",
                "phone" => "15620004000",
                "ip" => "192.168.1.1",
                "mac" => "06:19:C2:FA:36:2B",
                "education" => [
                    "primary_school" => "Qiankeng Xiaoxue",
                    "junior_middle_school" => "Qiankeng Zhongxue",
                    "high_school" => "Mianhu Gaozhong",
                    "university" => "Foshan University",
                ],
                "company" => [
                    "name" => "Qiankeng Company",
                    "website" => "https://www.qiankeng.com",
                    "country" => "China",
                    "addr" => "Foshan Nanhai Guicheng",
                    "postcode" => "532000",
                    "colleagues" => [
                        [
                            "name" => "Judy",
                            "position" => "Reception"
                        ],
                        [
                            "name" => "Sian",
                            "position" => "Financial"
                        ],
                        [
                            "name" => "Brook",
                            "position" => "JAVA"
                        ],
                        [
                            "name" => "Kurt",
                            "position" => "PHP"
                        ],
                    ],
                    "boss" => [
                        "Mike",
                        "David",
                        "Johnny",
                        "Extra",
                    ]
                ]
            ];
        }

        $rule = [
            "id" => "required|/^\d+$/",
            "name" => "required|string|len<=>[8,32]",
            "gender" => "required|(s)[male,female]",
            "dob" => "required|dob",
            "age" => "required|check_age[@gender,30] >> @this is wrong",
            "height" => [
                "[or]" => [
                    "required|=(@height_unit,cm)|<=>=[100,200] >> @this should be in [100,200] when height_unit is cm",
                    "required|=(@height_unit,m)|<=>=[1,2] >> @this should be in [1,2] when height_unit is m",
                ]
            ],
            "height_unit" => "required|(s)[cm,m]",
            "weight" => [
                "[or]" => [
                    "required|=(@weight_unit,kg)|<=>=[40,100]",
                    "required|=(@weight_unit,lb)|<=>[88,220]",
                ]
            ],
            "weight_unit" => "required|(s)[kg,lb]",
            "email" => "required|email",
            "phone" => "required|/(^1[3|4|5|6|7|8|9]\d{9}$)|(^09\d{8}$)/ >> phone number error",
            "ip" => "optional|ip",
            "mac" => "optional|mac",
            "education" => [
                "primary_school" => "required|=[Qiankeng Xiaoxue]",
                "junior_middle_school" => "required|!=[Foshan Zhongxue]",
                "high_school" => "if(=(@junior_middle_school,Mianhu Zhongxue))|required|len>[8]",
                "university" => "!if(=(@junior_middle_school,Mianhu Zhongxue))|required|len>[8]",
            ],
            "company" => [
                "name" => "required|len<=>[8,64]",
                "website" => "required|url",
                "country" => "optional|len<=[32]",
                "addr" => "required|len>[16]",
                "postcode" => "optional|len<[16]|check_postcode(@parent)",
                "colleagues" => [
                    ".*" => [
                        "name" => "required|string|len<=>[3,32]",
                        "position" => "required|(s)[Reception,Financial,PHP,JAVA]"
                    ]
                ],
                "boss" => [
                    "required|=[Mike]",
                    "required|(s)[Johnny,David]",
                    "optional|(s)[Johnny,David]"
                ]
            ],
            "favourite_food" => [
                "[optional].*" => [
                    "name" => "required|string",
                    "place_name" => "optional|string"
                ]
            ]
        ];

        $validation_conf = [
            'language' => 'en-us',
            'validation_global' => true,
        ];

        return $this->validate($data, $rule, $validation_conf);
    }

    public function error($data = array())
    {
        if (empty($data)) {
            $data = [
                "id" => "",
                "name" => "12",
                "gender" => "female2",
                "dob" => "2000-01-01",
                "age" => 11,
                "height" => 1.65,
                "height_unit" => "cm",
                "weight" => 80,
                "weight_unit" => "lb1",
                "email" => "10000@qq.com.123@qq",
                "phone" => "15620004000-",
                "ip" => "192.168.1.1111",
                "mac" => "06:19:C2:FA:36:2B111",
                "education" => [
                    "primary_school" => "???Qiankeng Xiaoxue",
                    "junior_middle_school" => "Foshan Zhongxue",
                    "high_school" => "Mianhu Gaozhong",
                    "university" => "Foshan",
                ],
                "company" => [
                    "name" => "Qianken",
                    "website" => "https://www.qiankeng.com1",
                    "country" => "US",
                    "addr" => "Foshan Nanhai",
                    "postcode" => "532000",
                    "colleagues" => [
                        [
                            "name" => 1,
                            "position" => "Reception"
                        ],
                        [
                            "name" => 2,
                            "position" => "Financial1"
                        ],
                        [
                            "name" => 3,
                            "position" => "JAVA"
                        ],
                        [
                            "name" => "Kurt",
                            "position" => "PHP1"
                        ],
                    ],
                    "boss" => [
                        "Mike1",
                        "David",
                        "Johnny2",
                        "Extra",
                    ]
                ],
                "favourite_food" => [
                    [
                        "name" => "HuoGuo",
                        "place_name" => "SiChuan"
                    ],
                    [
                        "name" => "Beijing Kaoya",
                        "place_name" => "Beijing"
                    ],
                ]
            ];
        }

        $rule = [
            "id" => 'required|/^\d+$/ >> { "required": "Users define - @this is required", "preg": "Users define - @this should be \"MATCHED\" @preg"}',
            "name" => "required|string|len<=>[8,32]",
            "gender" => "required|(s)[male,female]",
            "dob" => "required|dob",
            "age" => "required|check_age[@gender,30] >> @this is wrong",
            "height[or]" => [
                "required|=(@height_unit,cm)|<=>=[100,200] >> @this should be in [100,200] when height_unit is cm",
                "required|=(@height_unit,m)|<=>=[1,2] >> @this should be in [1,2] when height_unit is m",
            ],
            "height_unit" => "required|(s)[cm,m]",
            "weight[or]" => [
                "required|=(@weight_unit,kg)|<=>=[40,100] >> @this should be in [40,100] when height_unit is kg",
                "required|=(@weight_unit,lb)|<=>[88,220] >> @this should be in [88,220] when height_unit is lb",
            ],
            "weight_unit" => "required|(s)[kg,lb]",
            "email" => "required|email",
            "phone" => "required|/(^1[3|4|5|6|7|8|9]\d{9}$)|(^09\d{8}$)/ >> phone number error",
            "ip" => "optional|ip",
            "mac" => "optional|mac",
            "education" => [
                "primary_school" => "required|=[Qiankeng Xiaoxue]",
                "junior_middle_school" => "required|!=[Foshan Zhongxue]",
                "high_school" => "if(=(@junior_middle_school,Mianhu Zhongxue))|required|len>[18]",
                "university" => "!if(=(@junior_middle_school,Mianhu Zhongxue))|required|len>[8]",
            ],
            "company" => [
                "name" => "required|len<=>[8,64]",
                "website" => "required|url",
                "country" => "optional|len>=[6]",
                "addr" => "required|len>[16]",
                "postcode" => "optional|len<[16]|check_postcode(@parent)",
                "colleagues.*" => [
                    "name" => "required|string|len<=>[3,32]",
                    "position" => "required|(s)[Reception,Financial,PHP,JAVA]"
                ],
                "boss" => [
                    "required|=[Mike]",
                    "required|(s)[Johnny,David]",
                    "optional|(s)[Johnny,David]"
                ]
            ],
            "favourite_food[optional].*" => [
                "name" => "required|string",
                "place_name" => "optional|string"
            ]
        ];

        $validation_conf = [
            'language' => 'zh-cn',
            'validation_global' => true,
        ];

        return $this->validate($data, $rule, $validation_conf);
    }

    public function error2($data = array())
    {
        if (empty($data)) {
            $data = [
                "id" => "ABBC",
                "name" => "12",
                "gender" => "female2",
                "dob" => "2000-01-01",
                "age" => 11,
                "height" => 1.65,
                "height_unit" => "cm",
                "weight" => 80,
                "weight_unit" => "lb1",
                "email" => "10000@qq.com.123@qq",
                "phone" => "15620004000-",
                "ip" => "192.168.1.1111",
                "mac" => "06:19:C2:FA:36:2B111",
                "education" => [
                    "primary_school" => "???Qiankeng Xiaoxue",
                    "junior_middle_school" => "Foshan Zhongxue",
                    "high_school" => "Mianhu Gaozhong",
                    "university" => "Foshan",
                ],
                "company" => [
                    "name" => "Qianken",
                    "website" => "https://www.qiankeng.com1",
                    "country" => "US",
                    "addr" => "Foshan Nanhai",
                    "postcode" => "532000",
                    "colleagues" => [
                        [
                            "name" => 1,
                            "position" => "Reception"
                        ],
                        [
                            "name" => 2,
                            "position" => "Financial1"
                        ],
                        [
                            "name" => 3,
                            "position" => "JAVA"
                        ],
                        [
                            "name" => "Kurt",
                            "position" => "PHP1"
                        ],
                    ],
                    "boss" => [
                        "Mike1",
                        "David",
                        "Johnny2",
                        "Extra",
                    ]
                ],
                "favourite_food" => [
                    [
                        "name" => "HuoGuo",
                        "place_name" => "SiChuan"
                    ],
                    [
                        "name" => "Beijing Kaoya",
                        "place_name" => "Beijing"
                    ],
                ]
            ];
        }

        $rule = [
            "id" => "required|int|/^\d+$/",
            "name" => "required|string|len<=>[8,32]",
            "gender" => "required|(s)[male,female]",
            "dob" => "required|dob",
            "age" => "required|check_age[@gender,30] >> @this is wrong",
            "height" => [
                "[or]" => [
                    "required|=(@height_unit,cm)|<=>=[100,200] >> @this should be in [100,200] when height_unit is cm",
                    "required|=(@height_unit,m)|<=>=[1,2] >> @this should be in [1,2] when height_unit is m",
                ]
            ],
            "height_unit" => "required|(s)[cm,m]",
            "weight[or]" => [
                "[or]" => [
                    "required|=(@weight_unit,kg)|<=>=[40,100] >> @this should be in [40,100] when height_unit is kg",
                    "required|=(@weight_unit,lb)|<=>[88,220] >> @this should be in [88,220] when height_unit is lb",
                ]
            ],
            "weight_unit" => "required|(s)[kg,lb]",
            "email" => "required|email",
            "phone" => "required|/(^1[3|4|5|6|7|8|9]\d{9}$)|(^09\d{8}$)/ >> phone number error",
            "ip" => "optional|ip",
            "mac" => "optional|mac",
            "education" => [
                "primary_school" => "required|=[Qiankeng Xiaoxue]",
                "junior_middle_school" => "required|!=[Foshan Zhongxue]",
                "high_school" => "if(=(@junior_middle_school,Mianhu Zhongxue))|required|len>[18]",
                "university" => "!if(=(@junior_middle_school,Mianhu Zhongxue))|required|len>[8]",
            ],
            "company" => [
                "name" => "required|len<=>[8,64]",
                "website" => "required|url",
                "country" => "optional|len>=[6]",
                "addr" => "required|len>[16]",
                "postcode" => "optional|len<[16]|check_postcode(@parent)",
                "colleagues" => [
                    ".*" => [
                        "name" => "required|string|len<=>[3,32]",
                        "position" => "required|(s)[Reception,Financial,PHP,JAVA]"
                    ]
                ],
                "boss" => [
                    "required|=[Mike]",
                    "required|(s)[Johnny,David]",
                    "optional|(s)[Johnny,David]"
                ]
            ],
            "favourite_food" => [
                "[optional].*" => [
                    "name" => "required|string",
                    "place_name" => "optional|string"
                ]
            ]
        ];

        $validation_conf = [
            'language' => 'zh-cn',
            'validation_global' => true,
        ];

        return $this->validate($data, $rule, $validation_conf);
    }

    public function readme_case($data = array())
    {
        if (empty($data)) {
            $data = [
                "id" => "",
                "name" => "12",
                "email" => "10000@qq.com.123@qq",
                "phone" => "15620004000-",
                "education" => [
                    "primary_school" => "???Qiankeng Xiaoxue",
                    "junior_middle_school" => "Foshan Zhongxue",
                    "high_school" => "Mianhu Gaozhong",
                    "university" => "Foshan",
                ],
                "company" => [
                    "name" => "Qianken",
                    "website" => "https://www.qiankeng.com1",
                    "colleagues" => [
                        [
                            "name" => 1,
                            "position" => "Reception"
                        ],
                        [
                            "name" => 2,
                            "position" => "Financial1"
                        ],
                        [
                            "name" => 3,
                            "position" => "JAVA"
                        ],
                        [
                            "name" => "Kurt",
                            "position" => "PHP1"
                        ],
                    ],
                    "boss" => [
                        "Mike1",
                        "David",
                        "Johnny2",
                        "Extra",
                    ]
                ],
                "favourite_food" => [
                    [
                        "name" => "HuoGuo",
                        "place_name" => "SiChuan"
                    ],
                    [
                        "name" => "Beijing Kaoya",
                        "place_name" => "Beijing"
                    ],
                ]
            ];
        }

        $rule = [
            // id 是必要的且必须匹配正则 /^\d+$/， >> 后面的required 和 正则对应的报错信息
            "id" => 'required|/^\d+$/ >> { "required": "用户自定义 - @this 是必要的", "preg": "用户自定义 - @this 必须匹配 @preg" }',
            // name 是必要的且必须是字符串且长度在区间 【8，32)
            "name" => "required|string|len<=>[8,32]",
            "email" => "required|email",
            "phone" => "required|/(^1[3|4|5|6|7|8|9]\d{9}$)|(^09\d{8}$)/ >> 用户自定义 - phone number 错误",
            // ip 是可选的
            "ip" => "optional|ip",
            "education" => [
                // education.primary_school 必须等于 “Qiankeng Xiaoxue”
                "primary_school" => "required|=[Qiankeng Xiaoxue]",
                "junior_middle_school" => "required|!=[Foshan Zhongxue]",
                "high_school" => "optional|string",
                "university" => "optional|string",
            ],
            "company" => [
                "name" => "required|len<=>[8,64]",
                "website" => "required|url",
                "colleagues.*" => [
                    "name" => "required|string|len<=>[3,32]",
                    // company.colleagues.*.position 必须等于 Reception,Financial,PHP,JAVA 其中之一
                    "position" => "required|(s)[Reception,Financial,PHP,JAVA]"
                ],
                // 以下三个规则只对 boss.0, boss.1, boss.2 有效，boss.3 及其他都无效 
                "boss" => [
                    "required|=[Mike]",
                    "required|(s)[Johnny,David]",
                    "optional|(s)[Johnny,David]"
                ]
            ],
            // favourite_food 是可选的索引数组，允许为空
            "favourite_food[optional].*" => [
                // favourite_food.*.name 必须是字符串
                "name" => "required|string",
                "place_name" => "optional|string"
            ]
        ];

        $validation_conf = [
            'language' => 'zh-cn',
            'validation_global' => true,
        ];

        return $this->validate($data, $rule, $validation_conf);
    }

    protected function validate($data, $rule, $validation_conf = array())
    {
        $validation = new Validation($validation_conf);

        $validation->add_method('check_postcode', function ($company) {
            if (isset($company['country']) && $company['country'] == "US") {
                if (!isset($company['postcode']) || $company['postcode'] != "123") {
                    // return false;
                    // return "#### check_postcode method error message(@this)";
                    return array(
                        'error_type' => 'server_error',
                        'message' => '*** check_postcode method error message(@this)',
                        "extra" => "extra message"
                    );
                }
            }

            return true;
        });

        if ($validation->set_rules($rule)->validate($data)) {
            return $validation->get_result();
        } else {
            // return $validation->get_error(true, false);
            // return $validation->get_error(Validation::ERROR_FORMAT_NESTED_DETAILED);
            // return $validation->get_error(Validation::ERROR_FORMAT_NESTED_GENERAL);
            // return $validation->get_error(Validation::ERROR_FORMAT_DOTTED_DETAILED);
            return $validation->get_error(Validation::ERROR_FORMAT_DOTTED_GENERAL);
        }
    }
}
