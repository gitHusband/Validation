<?php

require_once(__DIR__."/../Validation.php");
use githusband\Validation;

function check_age($data, $gender, $param)
{
    if ($gender == "male") {
        if ($data > $param) return false;
    }else {
        if ($data < $param) return false;
    }

    return true;
}

class Tests
{

    public function __construct($config=array())
    {

    }

    public function run($data = array())
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
            "id" => "*|int|/^\d+$/",
            "name" => "*|string|len<=>:8,32",
            "gender" => "*|(s):male,female",
            "dob" => "*|dob",
            "age" => "*|check_age:@gender,30 >> @me is wrong",
            "height[||]" => [
                "*|=::@height_unit,cm|<=>=:100,200 >> @me should be in [100,200] when height_unit is cm",
                "*|=::@height_unit,m|<=>=:1,2 >> @me should be in [1,2] when height_unit is m",
            ],
            "height_unit" => "*|(s):cm,m",
            "weight[||]" => [
                "*|=::@weight_unit,kg|<=>=:40,100",
                "*|=::@weight_unit,lb|<=>:88,220",
            ],
            "weight_unit" => "*|(s):kg,lb",
            "email" => "*|email",
            "phone" => "*|/(^1[3|4|5|6|7|8|9]\d{9}$)|(^09\d{8}$)/ >> phone number error",
            "ip" => "O|ip",
            "mac" => "O|mac",
            "education" => [
                "primary_school" => "*|=:Qiankeng Xiaoxue",
                "junior_middle_school" => "*|!=:Foshan Zhongxue",
                "high_school" => "if?=::@junior_middle_school,Mianhu Zhongxue|*|len>:8",
                "university" => "if0?=::@junior_middle_school,Mianhu Zhongxue|*|len>:8",
            ],
            "company" => [
                "name" => "*|len<=>:8,64",
                "website" => "*|url",
                "country" => "O|len<=:32",
                "addr" => "*|len>:16",
                "postcode" => "O|len<:16|check_postcode::@parent",
                "colleagues.*" => [
                    "name" => "*|string|len<=>:3,32",
                    "position" => "*|(s):Reception,Financial,PHP,JAVA"
                ],
                "boss" => [
                    "*|=:Mike",
                    "*|(s):Johnny,David",
                    "O|(s):Johnny,David"
                ]
            ],
            "favourite_food[O].*" => [
                "name" => "*|string",
                "place_name" => "O|string" 
            ]
        ];

        $validation_conf = [
            'language' => 'en-us',
            'validation_global' => true, 
        ];

        return $this->validate($data, $rule, $validation_conf);
    }

    public function run2($data = array())
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
            "id" => "*|int|/^\d+$/",
            "name" => "*|string|len<=>:8,32",
            "gender" => "*|(s):male,female",
            "dob" => "*|dob",
            "age" => "*|check_age:@gender,30 >> @me is wrong",
            "height" => [
                "[||]" => [
                    "*|=::@height_unit,cm|<=>=:100,200 >> @me should be in [100,200] when height_unit is cm",
                    "*|=::@height_unit,m|<=>=:1,2 >> @me should be in [1,2] when height_unit is m",
                ]
            ],
            "height_unit" => "*|(s):cm,m",
            "weight" => [
                "[||]" => [
                    "*|=::@weight_unit,kg|<=>=:40,100",
                    "*|=::@weight_unit,lb|<=>:88,220",
                ]
            ],
            "weight_unit" => "*|(s):kg,lb",
            "email" => "*|email",
            "phone" => "*|/(^1[3|4|5|6|7|8|9]\d{9}$)|(^09\d{8}$)/ >> phone number error",
            "ip" => "O|ip",
            "mac" => "O|mac",
            "education" => [
                "primary_school" => "*|=:Qiankeng Xiaoxue",
                "junior_middle_school" => "*|!=:Foshan Zhongxue",
                "high_school" => "if?=::@junior_middle_school,Mianhu Zhongxue|*|len>:8",
                "university" => "if0?=::@junior_middle_school,Mianhu Zhongxue|*|len>:8",
            ],
            "company" => [
                "name" => "*|len<=>:8,64",
                "website" => "*|url",
                "country" => "O|len<=:32",
                "addr" => "*|len>:16",
                "postcode" => "O|len<:16|check_postcode::@parent",
                "colleagues" => [
                    ".*" => [
                        "name" => "*|string|len<=>:3,32",
                        "position" => "*|(s):Reception,Financial,PHP,JAVA"
                    ]
                ],
                "boss" => [
                    "*|=:Mike",
                    "*|(s):Johnny,David",
                    "O|(s):Johnny,David"
                ]
            ],
            "favourite_food" => [
                "[O].*" => [
                    "name" => "*|string",
                    "place_name" => "O|string"
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
            "id" => '*|/^\d+$/ >> { "*": "Users define - @me is required", "preg": "Users define - @me should be \"MATCHED\" @preg"}',
            "name" => "*|string|len<=>:8,32",
            "gender" => "*|(s):male,female",
            "dob" => "*|dob",
            "age" => "*|check_age:@gender,30 >> @me is wrong",
            "height[||]" => [
                "*|=::@height_unit,cm|<=>=:100,200 >> @me should be in [100,200] when height_unit is cm",
                "*|=::@height_unit,m|<=>=:1,2 >> @me should be in [1,2] when height_unit is m",
            ],
            "height_unit" => "*|(s):cm,m",
            "weight[||]" => [
                "*|=::@weight_unit,kg|<=>=:40,100 >> @me should be in [40,100] when height_unit is kg",
                "*|=::@weight_unit,lb|<=>:88,220 >> @me should be in [88,220] when height_unit is lb",
            ],
            "weight_unit" => "*|(s):kg,lb",
            "email" => "*|email",
            "phone" => "*|/(^1[3|4|5|6|7|8|9]\d{9}$)|(^09\d{8}$)/ >> phone number error",
            "ip" => "O|ip",
            "mac" => "O|mac",
            "education" => [
                "primary_school" => "*|=:Qiankeng Xiaoxue",
                "junior_middle_school" => "*|!=:Foshan Zhongxue",
                "high_school" => "if?=::@junior_middle_school,Mianhu Zhongxue|*|len>:18",
                "university" => "if0?=::@junior_middle_school,Mianhu Zhongxue|*|len>:8",
            ],
            "company" => [
                "name" => "*|len<=>:8,64",
                "website" => "*|url",
                "country" => "O|len>=:6",
                "addr" => "*|len>:16",
                "postcode" => "O|len<:16|check_postcode::@parent",
                "colleagues.*" => [
                    "name" => "*|string|len<=>:3,32",
                    "position" => "*|(s):Reception,Financial,PHP,JAVA"
                ],
                "boss" => [
                    "*|=:Mike",
                    "*|(s):Johnny,David",
                    "O|(s):Johnny,David"
                ]
            ],
            "favourite_food[O].*" => [
                "name" => "*|string",
                "place_name" => "O|string" 
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
            "id" => "*|int|/^\d+$/",
            "name" => "*|string|len<=>:8,32",
            "gender" => "*|(s):male,female",
            "dob" => "*|dob",
            "age" => "*|check_age:@gender,30 >> @me is wrong",
            "height" => [
                "[||]" => [
                    "*|=::@height_unit,cm|<=>=:100,200 >> @me should be in [100,200] when height_unit is cm",
                    "*|=::@height_unit,m|<=>=:1,2 >> @me should be in [1,2] when height_unit is m",
                ]
            ],
            "height_unit" => "*|(s):cm,m",
            "weight[||]" => [
                "[||]" => [
                    "*|=::@weight_unit,kg|<=>=:40,100 >> @me should be in [40,100] when height_unit is kg",
                    "*|=::@weight_unit,lb|<=>:88,220 >> @me should be in [88,220] when height_unit is lb",
                ]
            ],
            "weight_unit" => "*|(s):kg,lb",
            "email" => "*|email",
            "phone" => "*|/(^1[3|4|5|6|7|8|9]\d{9}$)|(^09\d{8}$)/ >> phone number error",
            "ip" => "O|ip",
            "mac" => "O|mac",
            "education" => [
                "primary_school" => "*|=:Qiankeng Xiaoxue",
                "junior_middle_school" => "*|!=:Foshan Zhongxue",
                "high_school" => "if?=::@junior_middle_school,Mianhu Zhongxue|*|len>:18",
                "university" => "if0?=::@junior_middle_school,Mianhu Zhongxue|*|len>:8",
            ],
            "company" => [
                "name" => "*|len<=>:8,64",
                "website" => "*|url",
                "country" => "O|len>=:6",
                "addr" => "*|len>:16",
                "postcode" => "O|len<:16|check_postcode::@parent",
                "colleagues" => [
                    ".*" => [
                        "name" => "*|string|len<=>:3,32",
                        "position" => "*|(s):Reception,Financial,PHP,JAVA"
                    ]
                ],
                "boss" => [
                    "*|=:Mike",
                    "*|(s):Johnny,David",
                    "O|(s):Johnny,David"
                ]
            ],
            "favourite_food" => [
                "[O].*" => [
                    "name" => "*|string",
                    "place_name" => "O|string"
                ]
            ]
        ];

        $validation_conf = [
            'language' => 'zh-cn',
            'validation_global' => true,
        ];

        return $this->validate($data, $rule, $validation_conf);
    }

    protected function validate($data, $rule, $validation_conf=array())
    {
        $validation = new Validation($validation_conf);

        $validation->add_method('check_postcode', function($company) {
            if (isset($company['country']) && $company['country'] == "US"){
                if (!isset($company['postcode']) || $company['postcode'] != "123"){
                    // return false;
                    // return "#### check_postcode method error message(@me)";
                    return array(
                        'error_type' => 'server_error',
                        'message' => '*** check_postcode method error message(@me)',
                        "extra" => "extra message"
                    );
                }
            }

            return true;
        });

        if ($validation->set_rules($rule)->validate($data)) {
            return $validation->get_result();
        }else {
            // return $validation->get_result();
            return $validation->get_error(true, false);
        }
    }
}

$method = isset($argv[1])? $argv[1] : "error";

$test = new Tests();

if (method_exists($test, $method)) {
    $result = call_user_func_array([$test, $method], []);
}else {
    echo "Error test method {$method}.\n";
    die;
}

print_r($result);