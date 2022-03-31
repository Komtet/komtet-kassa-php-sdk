<?php

/**
* This file is part of the komtet/kassa-sdk library
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace KomtetTest\KassaSdk\v2;

use Komtet\KassaSdk\v2\CorrectionInfo;
use PHPUnit\Framework\TestCase;

class CorrectionInfoTest extends TestCase
{
    public function testCorrectionInfo()
    {
        $correction_info = new CorrectionInfo('self', '31.01.2021', '1', 'Наименование документа основания для коррекции');
        $this->assertEquals($correction_info->asArray(), 
                            ['type' => 'self', 
                            'base_date' => '31.01.2021',
                            'base_number' => '1', 
                            'base_name' => 'Наименование документа основания для коррекции']);
    }

    public function testCreateSelf()
    {
        $correction_info = CorrectionInfo::createSelf('31.01.2021', '1', 'Наименование документа основания для коррекции');
        $this->assertEquals($correction_info->asArray(), 
                            ['type' => 'self',
                            'base_date' => '31.01.2021',
                            'base_number' => '1', 
                            'base_name' => 'Наименование документа основания для коррекции']);
    }

    public function testCreateInstruction()
    {
        $correction_info = CorrectionInfo::createInstruction('31.01.2021', '1', 'Наименование документа основания для коррекции');
        $this->assertEquals($correction_info->asArray(), 
                            ['type' => 'instruction',
                            'base_date' => '31.01.2021',
                            'base_number' => '1', 
                            'base_name' => 'Наименование документа основания для коррекции']);
    }

}