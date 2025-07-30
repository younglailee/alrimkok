<?php
/**
 * 사용자모드 > 방문기록 모듈 클래스
 * @file    VisitUser.class.php
 * @author  Alpha-Edu
 * @package visit
 */
namespace sFramework;

class VisitMobile extends Visit
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
        return false;
    }
}
