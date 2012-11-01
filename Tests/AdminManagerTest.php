<?php

namespace Snowcap\AdminBundle\Tests;

use Snowcap\AdminBundle\AdminManager;

class AdminManagerTest extends \PHPUnit_Framework_TestCase {
    /**
     * Simple trivial test to check admin registration
     *
     */
    public function testRegisterAdmin()
    {
        $adminManager = new AdminManager();
        $admin = $this->getMock('Snowcap\AdminBundle\Admin\AdminInterface');
        $adminManager->registerAdmin('foo', $admin);

        $this->assertInstanceOf('Snowcap\AdminBundle\Admin\AdminInterface', $adminManager->getAdmin('foo'));
    }
}