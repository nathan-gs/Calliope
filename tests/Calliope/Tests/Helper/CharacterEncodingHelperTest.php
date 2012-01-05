<?php

namespace Calliope\Tests\Helper;

use Calliope\Helper\CharacterEncodingHelper;

/**
 * @group helper
 */
class CharacterEncodingHelperTest extends \PHPUnit_Framework_TestCase {

    public function provideSubjects() {
        return array(
            array('Test Tørn Tester <test@example.com>', '=?ISO-8859-1?Q?Test_T=F8rn_Tester?= <test@example.com>'),
            array('Fw: Relevé de compte - rappel Test Belgium sa 14/09/2011', '=?ISO-8859-1?Q?Fw=3A_Relev=E9_de_compte_-_rappel_Test_Belgium_sa?= =?ISO-8859-1?Q?_14=2F09=2F2011?='),
            array('Dringend copie conform 3334812170 - bedrag : 7.750,74 €', '=?UTF-8?B?RHJpbmdlbmQgY29waWUgY29uZm9ybSAzMzM0ODEyMTcwIC0gYmVkcmFnIDog?= =?UTF-8?B?Ny43NTAsNzQg4oKs?='),
            array('R: Relevé  de compte 14/09/2011', '=?Windows-1252?Q?R:_Relev=E9__de_compte_14/09/2011?='),
            array('RE: Relevé de compte - rappel Gree 15/09/2011', '=?utf-8?Q?RE:_Relev=C3=A9_de_compte_-_rappel_Gr?= =?utf-8?Q?ee_15/09/2011?='),
            array('RE: Test ë bvba 15/09/2011', '=?windows-1256?Q?RE=3A_Test_=EB_bvba_15/09/2?= =?windows-1256?Q?011?='),
            array('Betaling openstaande factuur', 'Betaling openstaande factuur'),
            array('', ''),
            
        );
    }

    /**
     * @dataProvider provideSubjects
     */
    public function testSubjects($expected, $source) {

        $this->assertEquals(
                $expected, CharacterEncodingHelper::header($source, CharacterEncodingHelper::UTF8)
        );
    }
    
    public function testText() {
        $text = "test met € en é en ë.";
        
        $this->assertEquals($text, CharacterEncodingHelper::text($text, CharacterEncodingHelper::UTF8));
    }

}