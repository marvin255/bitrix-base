<?php

class MainPageCest
{
    public function _before(AcceptanceTester $I)
    {
    }

    public function _after(AcceptanceTester $I)
    {
    }

    public function tryToSeeBodyElement(AcceptanceTester $I)
    {
        $I->amOnPage('/');
        $I->seeElement('body');
    }
}
