<?php
namespace SQL;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2014-10-01 at 21:54:41.
 */
class WordTokenTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var WordToken
     */
    protected $object;

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     */
    protected function setUp()
    {

    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     */
    protected function tearDown()
    {
    }

    /**
     * @covers SQL\WordToken::match
     * @todo   Implement testMatch().
     */
    public function testMatch()
    {
        $this->object = new WordToken("AND");
        $r = $this->object->match("AND", 0);
        $this->assertTrue($r);

        $this->object = new WordToken("AND");
        $r = $this->object->match("AND", 1);
        $this->assertFalse($r);

        $this->object = new WordToken("AND");
        $r = $this->object->match(",AND", 1);
        $this->assertTrue($r);

        $this->object = new WordToken("AND");
        $r = $this->object->match(",AND,", 1);
        $this->assertTrue($r);

        $this->object = new WordToken("AND");
        $r = $this->object->match(",ANDL", 1);
        $this->assertFalse($r);
    }

    /**
     * @covers SQL\WordToken::lexeme
     * @todo   Implement testLexeme().
     */
    public function testLexeme()
    {
        $this->object = new WordToken("AND");
        $this->assertEquals("AND", $this->object->lexeme());
    }

    /**
     * @covers SQL\WordToken::__toString
     * @todo   Implement test__toString().
     */
    public function test__toString()
    {
        $this->object = new WordToken("FROM");
        $str = $this->object->__toString();
        $this->assertEquals("FROM", $str);
    }
}
