<?php

namespace Calliope\Tests\Criteria;

use Calliope\Criteria\ImapCriteria;

/**
 * @group criteria
 */
class ImapCriteriaTest extends \PHPUnit_Framework_TestCase {

    public function provideSimple() {
        return array(
            array('unread', 'UNSEEN'),
            array('read', 'SEEN'),
        );
    }

    /**
     * @dataProvider provideSimple
     */
    public function testSimple($method, $result) {
        $criteria = new ImapCriteria();

        $methodResult = $criteria->$method();
        $this->assertEquals($criteria, $methodResult);
        $this->assertSame($result, $criteria->getCriteria());
    }

    public function provideSimpleWith1Argument() {
        $date = new \DateTime();
        
        return array(
            array('from', 'test@example.org', 'FROM "test@example.org"'),
            array('to', 'test@example.org', 'TO "test@example.org"'),
            array('cc', 'test@example.org', 'CC "test@example.org"'),
            array('bcc', 'test@example.org', 'BCC "test@example.org"'),
            array('before', $date, 'BEFORE "'.$date->format('j M Y, g.ia O').'"'),
            array('since', $date, 'SINCE "'.$date->format('j M Y, g.ia O').'"'),
            array('on', $date, 'ON "'.$date->format('j M Y, g.ia O').'"'),
            array('subject', 'Test subject', 'SUBJECT "Test subject"'),
            //array('subject', 'Test subject with special characters: êë€', \utf8_encode('SUBJECT "Test subject with special characters: êë€"')),
            array('text', 'Test text', 'TEXT "Test text"'),
            //array('text', 'TEXT "Test text with special characters: êë€"', \utf8_encode('Test text with special characters: êë€')),
            array('body', 'Body text', 'BODY "Body text"'),
            //array('body', 'BODY "Body text with special characters: êë€"', \utf8_encode('Body text with special characters: êë€')),
        );

    }

    /**
     * @dataProvider provideSimpleWith1Argument
     */
    public function testSimpleWith1Argument($method, $argument, $result) {
        $criteria = new ImapCriteria();

        $methodResult = $criteria->$method($argument);
        $this->assertEquals($criteria, $methodResult);
        $this->assertSame($result, $criteria->getCriteria());
    }

}