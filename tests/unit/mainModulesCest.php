<?php


use Bitrix\Main\Loader;

class mainModulesCest
{
    public function _before(UnitTester $I)
    {
    }

    public function _after(UnitTester $I)
    {
    }

    public function iApplication(UnitTester $I)
    {
        $I->assertInstanceOf(\Bitrix\Main\Application::class, \Bitrix\Main\Application::getInstance());
    }

    public function iBlock(UnitTester $I)
    {
        $I->assertTrue(Loader::includeModule('iblock'));
    }

    // FAIL
//    public function iVote(UnitTester $I)
//    {
//        $I->assertTrue(Loader::includeModule('vote'));
//    }

}
