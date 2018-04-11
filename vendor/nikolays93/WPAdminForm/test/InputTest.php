<?php

namespace NikolayS93\WPAdminForm;

$plugin_dir = Util::get_plugin_dir();

require_once $plugin_dir . '/vendor/phpunit/phpunit/src/Framework/TestCase.php';
require_once $plugin_dir . '/vendor/NikolayS93/WPAdminForm/src/Input.php';

class InputTest extends PHPUnit_Framework_TestCase {
    public function _is_checked()
    {
        // $test = new Input();
        // $this->assertEquals(8, $test->_is_checked(2, 3)); 
    }
}