<?php
namespace OCA\Metadata\Tests\Controller;

use OCA\Metadata\Controller\MetadataController;
use Test\TestCase;

class MetadataControllerTest extends TestCase {
    private $user;
    private $controller;

    public function setUp() {
        parent::setUp();

        $this->user = 'user_' . uniqid();
        $backend = new \Test\Util\User\Dummy();
        $backend->createUser($this->user, $this->user);
        \OC_User::useBackend($backend);

        \OC\Files\Filesystem::tearDown();
        \OC\Files\Filesystem::mount('\OC\Files\Storage\Local', array('datadir' => realpath(__DIR__ . '/../files')), '/' . $this->user . '/files');
        \OC\Files\Filesystem::init($this->user, '/' . $this->user . '/files');

        $this->loginAsUser($this->user);

        $this->controller = new MetadataController(
            'metadata',
            $this->createMock(\OCP\IRequest::class)
        );
    }

    public function testGet() {
        $res = $this->controller->get('a.txt');
        $data = $res->getData();
        $this->assertEquals('error', $data['response']);
        $metadata = $data['metadata'];
        $this->assertNull($metadata);

        $res = $this->controller->get('IMG_20170626_181110.jpg');
        $data = $res->getData();
        $this->assertEquals('success', $data['response']);
        $metadata = $data['metadata'];
        $this->assertNotNull($metadata);
        $this->assertEquals('2017-06-26 18:11:09', $metadata['Date taken']);
    }
}
