<?php
/**
 * 사용자모드 > 캐러셀 모듈 클래스
 * @file    FooterUser.class.php
 * @author  Alpha-Edu
 * @package carousel
 */
namespace sFramework;

class FooterUser extends Footer
{
    public function checkViewAuth($data)
    {
        return true;
    }

    public function checkWriteAuth()
    {
        return false;
    }

    public function checkUpdateAuth($uid)
    {
        return false;
    }

}
