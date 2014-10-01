<?php
namespace SQL;

/**
 * Generated by PHPUnit_SkeletonGenerator on 2014-10-01 at 21:44:12.
 */
class SimpleTokenTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var SimpleToken
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
     * @covers SQL\SimpleToken::match
     * @todo   Implement testMatch().
     */
    public function testMatch()
    {
        $this->object = new SimpleToken(",");
        $result =  $this->object->match("abc,op", 3);
        $this->assertTrue($result);

        $this->object = new SimpleToken("=");
        $result =  $this->object->match("abc=op", 3);
        $this->assertTrue($result);

        $this->object = new SimpleToken("(");
        $result =  $this->object->match("abc(op", 3);
        $this->assertTrue($result);


        $this->object = new SimpleToken(")");
        $result =  $this->object->match("abc(op)", 6);
        $this->assertTrue($result);

        $this->object = new SimpleToken(".");
        $result =  $this->object->match("abc(op.", 6);
        $this->assertTrue($result);

        $this->object = new SimpleToken("*");
        $result =  $this->object->match("abc(op*", 6);
        $this->assertTrue($result);

        $this->object = new SimpleToken("/");
        $result =  $this->object->match("abc(op/", 6);
        $this->assertTrue($result);
    }

    /**
     * @covers SQL\SimpleToken::lexeme
     * @todo   Implement testLexeme().
     */
    public function testLexeme()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers SQL\SimpleToken::__toString
     * @todo   Implement test__toString().
     */
    public function test__toString()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers SQL\SimpleToken::startWith
     * @todo   Implement testStartWith().
     */
    public function testStartWith()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers SQL\SimpleToken::endWith
     * @todo   Implement testEndWith().
     */
    public function testEndWith()
    {
        // Remove the following lines when you implement this test.
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }
}