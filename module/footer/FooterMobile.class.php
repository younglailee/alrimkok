<?php
/**
 * 사용자모드 > 캐러셀 모듈 클래스
 * @file    FooterUser.class.php
 * @author  Alpha-Edu
 * @package carousel
 */
namespace sFramework;

class FooterMobile extends Footer
{
    protected function setModuleConfig()
    {
        global $layout_uri;
        parent::setModuleConfig();
        $this->set('no_image', $layout_uri.'/img/img_no-image-popupzone.jpg');
    }
    protected function convertDetail($data)
    {
        global $layout;
        $data = parent::convertDetail($data);
        $data['cr_uri'] = str_replace('/webuser/','/web'.$layout.'/',$data['cr_uri']);
        return $data;
    }
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
