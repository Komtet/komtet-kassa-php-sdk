<?php

/**
 * This file is part of the komtet/kassa-sdk library
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace KomtetTest\KassaSdk\v2;

use Komtet\KassaSdk\v2\MarkCode;
use PHPUnit\Framework\TestCase;

class MarkCodeTest extends TestCase
{
    public function testMarkCodeSuccess()
    {
        $mark_code = new MarkCode(MarkCode::GS1M, 'qweqeq12313sfdsgfdfyrt3333');
        $this->assertEquals($mark_code->asArray(), 
                            ['gs1m' => 'qweqeq12313sfdsgfdfyrt3333']);
    }

    public function testMarkCodeSuccess2()
    {
        $mark_code = new MarkCode(MarkCode::SHORT, 'sdfdsghtyuAAwe<gGGRTR%@$%DFt567rsd325rwf');
        $this->assertEquals($mark_code->asArray(), 
                            ['short' => 'sdfdsghtyuAAwe<gGGRTR%@$%DFt567rsd325rwf']);
    }

}
