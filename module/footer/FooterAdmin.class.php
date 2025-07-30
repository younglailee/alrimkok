<?php
/**
 * 관리자 > 캐러셀 모듈 클래스
 * @file    FooterAdmin.class.php
 * @author  Alpha-Edu
 * @package carousel
 */
namespace sFramework;

class FooterAdmin extends Footer
{
    public function checkViewAuth($data)
    {
        return true;
    }

    public function checkWriteAuth()
    {
        return true;
    }

    public function checkUpdateAuth($uid)
    {
        return true;
    }
}
