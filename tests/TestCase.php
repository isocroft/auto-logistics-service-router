<?phph

namespace Isocroft\AutoServiceRouter\Tests;

use Orchestra\Testbench\TestCase as Orchestra;

class TestCase extends Orchestra
{
    protected function setUp()
    {
        parent::setUp();
        
        $this->setUpAttachments();
    }
    
    protected function setUpAttachments()
    {
    
    }

}
