<?php

namespace githusband\Test;

class TestCommon
{
    public function help()
    {
        $help_data = [];

        $class_methods = get_class_methods($this);

        foreach ($class_methods as $method_name) {
            if (preg_match('/^test_.*/', $method_name)) {
                $help_data['test_methods'][] = $method_name;
            }
        }

        echo "Available test methods: \n";
        foreach ($help_data['test_methods'] as $key => $value) {
            echo "  - {$value}\n";
        }
    }
}
