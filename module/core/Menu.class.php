<?php
/**
 * 메뉴 정보 제어 클래스
 * @file    Menu.class.php
 * @author  Alpha-Edu
 * @package core
 */

namespace sFramework;

class Menu
{
    private $all_menu_arr = null;
    private $pure_menu_arr = null;
    private $this_uri = null;

    private $title_arr = array();
    private $page_no_arr = array();

    private $body_class = null;

    public function __construct($menu, $this_uri)
    {
        $this->all_menu_arr = $this->makeAllMenuArray($menu);
        $this->pure_menu_arr = $this->makePureMenuArray($this->all_menu_arr);
        $this->this_uri = $this_uri;

        $this->findPageInfo($this->all_menu_arr);
        $this->title_arr = array_reverse($this->title_arr);
        $this->page_no_arr = array_reverse($this->page_no_arr);
    }

    /**
     * 현재 페이지가 어떤 메뉴에 해당하는지 찾기
     * @param $arr
     * @return bool
     */
    private function findPageInfo($arr)
    {
        for ($i = 0; $i < count($arr); $i++) {
            if (is_array($arr[$i]['sub'])) {
                $flag_match = $this->findPageInfo($arr[$i]['sub']);
            } elseif (strpos($arr[$i]['uri'], $this->this_uri) > -1) {
                $flag_match = true;
            }

            if ($flag_match) {
                $this->title_arr[] = $arr[$i]['title'];
                $this->page_no_arr[] = $i + 1;

                if ($arr[$i]['body_class']) {
                    $this->body_class = $arr[$i]['body_class'];
                }

                return true;
            }
        }

        return false;
    }

    /**
     * 권한에 적합한 전체 메뉴 배열 생성성
     * @param $arr
     * @return array
     */
    private function makeAllMenuArray($arr)
    {
        global $member, $is_root;
        unset($menu);
        $menu_choice = array();

        if ($member['mb_auth_codes'] == 'promotion') {
            $menu_choice = array(
                '/webadmin/news/list.html',
                '/webadmin/article/list.html',
                '/webadmin/photo/list.html'
            );
        }

        for ($i = 0; $i < count($arr); $i++) {
            $flag_auth = false;
            if (is_array($arr[$i]['sub']) && count($arr[$i]['sub']) > 0) {
                $sub_arr = $this->makeAllMenuArray($arr[$i]['sub']);
                if (is_array($sub_arr) && count($sub_arr) > 0) {
                    $flag_auth = true;
                    $arr[$i]['sub'] = $sub_arr;
                    if ($arr[$i]['sub'][0]['uri']) {
                        $arr[$i]['uri'] = $arr[$i]['sub'][0]['uri'];
                    }
                }
            } elseif ($arr[$i]['uri']) {
                if (!$arr[$i]['auth_code']) {
                    // 메뉴에 특정 권한 코드가 없다면 권한 : true
                    $flag_auth = true;
                } else {
                    // 메뉴에 특정 권한 코드가 있을 경우는 선택적으로 처리
                    if ($is_root) {
                        $flag_auth = true;
                    } elseif (strpos('|' . $member['mb_auth_codes'] . '|', '|' . $arr[$i]['auth_code'] . '|') > -1) {
                        // 권한 코드가 부여된 계정이면 권한 부여
                        $flag_auth = true;
                    } else {
                        if ($member['mb_level'] == 5 || $member['mb_level'] == 4) {
                            $flag_auth = true;
                        } else {
                            // 권한이 없을 경우
                            $flag_auth = false;
                        }
                    }
                }
            }

            // 권한이 있을 경우만, 메뉴에 출력
            if ($flag_auth) {
                $menu[] = $arr[$i];
            }
        }

        //Log::debug($menu);

        return $menu;
    }

    /**
     * 기타 메뉴 제외한 메뉴 배열 생성
     * @param $arr
     * @return array
     */
    private function makePureMenuArray($arr)
    {
        unset($menu);
        for ($i = 0; $i < count($arr); $i++) {
            $flag_auth = true;
            if ($arr[$i]['is_etc']) {
                $flag_auth = false;
            } elseif (is_array($arr[$i]['sub']) && count($arr[$i]['sub']) > 0) {
                $sub_arr = $this->makePureMenuArray($arr[$i]['sub']);
                if (!$sub_arr || !count($sub_arr)) {
                    $flag_auth = false;
                }
            }

            if ($flag_auth) {
                $menu[] = $arr[$i];
            }
        }

        return $menu;
    }

    /**
     * 페이지 번호 배열 반환
     * @return array
     */
    public function getPageNoArr()
    {
        return $this->page_no_arr;
    }

    /**
     * 문서 제목을 반환
     * @return string
     */
    public function getDocumentTitle()
    {
        return $this->title_arr[count($this->title_arr) - 1];
    }

    /**
     * 그룹(1차 메뉴) 제목을 반환
     * @return string
     */
    public function getGroupTitle()
    {
        return $this->title_arr[0];
    }

    /**
     * <body /> 클래스를 반환
     * @return null
     */
    public function getBodyClass()
    {
        return $this->body_class;
    }

    /**
     * <html /> 제목을 반환
     * @param $doc_title
     * @return string
     */
    public function makeHtmlTitle($doc_title)
    {
        if (count($this->title_arr) > 0) {
            $html_title = implode(' &lt; ', array_reverse($this->title_arr));
        } else {
            $html_title = $doc_title;
        }

        if (defined('_HOMEPAGE_TITLE_')) {
            $html_title .= ' :: ' . _HOMEPAGE_TITLE_;
        }

        return $html_title;
    }

    /**
     * 제목 경로 생성
     * @param $home
     * @return string
     */
    public function makeTitlePath($home)
    {
        $title_path = $home;
        $menu_arr = $this->all_menu_arr;
        for ($i = 0; $i < count($this->title_arr); $i++) {
            $title = $this->title_arr[$i];
            $page_idx = $this->page_no_arr[$i] - 1;
            $href = ($menu_arr[$page_idx]['uri']) ? $menu_arr[$page_idx]['uri'] : '#';
            $title_path .= '<a href="' . $href . '" title="' . $title . '">' . $title . '</a>';
            $menu_arr = $menu_arr[$page_idx]['sub'];
        }

        return $title_path;
    }

    /**
     * 네비게이션 생성
     * @param $menu
     * @param int $max_depth
     * @return string
     */
    private function makeNavigation($menu, $max_depth = 3)
    {
        $nav = '';
        global $member;
        // menu 배열 검사
        if (is_array($menu) && count($menu) > 0) {
            for ($i = 0; $i < count($menu); $i++) {
                if ($menu[$i]['uri'] || count($menu[$i]['sub']) > 0) {
                    $nav .= '<li class="nav' . ($i + 1) . '">' . "\n";
                    $nav .= '<!-- depth1 -->' . "\n";
                    $nav .= '<a href="' . $menu[$i]['uri'] . '" title="' . $menu[$i]['title'] . '">';
                    $nav .= $menu[$i]['title'] . '</a>' . "\n";

                    if (is_array($menu[$i]['sub'])) {
                        if ($max_depth > 1 && count($menu[$i]['sub']) > 0) {
                            $nav .= '<ul>' . "\n";
                            for ($j = 0; $j < count($menu[$i]['sub']); $j++) {
                                $nav .= '<li class="nav' . ($j + 1) . '">' . "\n";
                                $nav .= '<!-- depth2 -->' . "\n";
                                $nav .= '<a href="' . $menu[$i]['sub'][$j]['uri'] . '" ';
                                $nav .= 'title="' . $menu[$i]['sub'][$j]['title'] . '">';
                                $nav .= $menu[$i]['sub'][$j]['title'] . '</a>' . "\n";
                                // 2 단계 하위 메뉴 배열 검사
                                if (is_array($menu[$i]['sub'][$j]['sub'])) {
                                    if ($max_depth > 2 && count($menu[$i]['sub'][$j]['sub']) > 0) {
                                        $nav .= '<ul>' . "\n";
                                        for ($k = 0; $k < count($menu[$i]['sub'][$j]['sub']); $k++) {
                                            $nav .= '<li class="nav' . ($k + 1) . '">' . "\n";
                                            $nav .= '<!-- depth3 -->' . "\n";
                                            $nav .= '<a href="' . $menu[$i]['sub'][$j]['sub'][$k]['uri'] . '" ';
                                            $nav .= 'title="' . $menu[$i]['sub'][$j]['sub'][$k]['title'] . '">';
                                            $nav .= $menu[$i]['sub'][$j]['sub'][$k]['title'] . '</a>' . "\n";
                                            $nav .= '<!-- //depth3 -->' . "\n";
                                            $nav .= '</li>' . "\n";
                                        }
                                        $nav .= '</ul>' . "\n";
                                    }
                                }

                                $nav .= '<!-- //depth2 -->' . "\n";
                                $nav .= '</li>' . "\n";
                            }
                            $nav .= '</ul>' . "\n";
                        }
                    }
                    $nav .= '<!-- //depth1 -->' . "\n";
                    $nav .= '</li>' . "\n";
                }
            }
        }

        return $nav;
    }

    /**
     * 모바일 네비게이션 생성
     * @param $menu
     * @param int $max_depth
     * @return string
     */
    private function makeMobileNavigation($menu, $max_depth = 3)
    {
        $nav = '';
        for ($i = 0; $i < count($menu); $i++) {
            if ($menu[$i]['uri'] || count($menu[$i]['sub']) > 0) {
                $nav .= '<li class="nav' . ($i + 1) . '">' . "\n";
                $nav .= '<!-- depth1 -->' . "\n";
                $nav .= '<a href="' . $menu[$i]['uri'] . '" title="' . $menu[$i]['title'] . '">';
                $nav .= $menu[$i]['title'] . '<i class="fa fa-angle-down"></i></a>' . "\n";

                if ($max_depth > 1 && count($menu[$i]['sub']) > 0) {
                    $nav .= '<ul>' . "\n";
                    for ($j = 0; $j < count($menu[$i]['sub']); $j++) {
                        $nav .= '<li class="nav' . ($j + 1) . '">' . "\n";
                        $nav .= '<!-- depth2 -->' . "\n";
                        $nav .= '<a href="' . $menu[$i]['sub'][$j]['uri'] . '" ';
                        $nav .= 'title="' . $menu[$i]['sub'][$j]['title'] . '">';
                        $nav .= $menu[$i]['sub'][$j]['title'] . '</a>' . "\n";

                        if ($max_depth > 2 && count($menu[$i]['sub'][$j]['sub']) > 0) {
                            $nav .= '<ul>' . "\n";
                            for ($k = 0; $k < count($menu[$i]['sub'][$j]['sub']); $k++) {
                                $nav .= '<li class="nav' . ($k + 1) . '">' . "\n";
                                $nav .= '<!-- depth3 -->' . "\n";
                                $nav .= '<a href="' . $menu[$i]['sub'][$j]['sub'][$k]['uri'] . '" ';
                                $nav .= 'title="' . $menu[$i]['sub'][$j]['sub'][$k]['title'] . '">';
                                $nav .= $menu[$i]['sub'][$j]['sub'][$k]['title'] . '</a>' . "\n";
                                $nav .= '<!-- //depth3 -->' . "\n";
                                $nav .= '</li>' . "\n";
                            }
                            $nav .= '</ul>' . "\n";
                        }

                        $nav .= '<!-- //depth2 -->' . "\n";
                        $nav .= '</li>' . "\n";
                    }
                    $nav .= '</ul>' . "\n";
                }

                $nav .= '<!-- //depth1 -->' . "\n";
                $nav .= '</li>' . "\n";
            }
        }

        return $nav;
    }

    /**
     * 네비게이션 생성
     * @param $menu
     * @param int $max_depth
     * @return string
     */
    private function makeAdminNavigation($menu, $max_depth = 3)
    {
        $nav = '';
        global $member;

        // 권한7 담당자 전용 설정
        $menu_sexualreport = array();
        if ($member['mb_level'] == 7) {
            if (strpos($member['mb_auth_codes'], 'receipt') !== false && strpos($member['mb_auth_codes'], 'sexualreport') !== false) {
                $auth_code = 'dual';
            } elseif ($member['mb_auth_codes'] == 'sexualreport') {
                $auth_code = 'sexualreport';

            } elseif ($member['mb_auth_codes'] == 'receipt') {
                $auth_code = 'receipt';
            }
            if ($auth_code == 'sexualreport' || $auth_code == 'dual') {
                $menu_sexualreport = array(
                    '/webadmin/finance/list.html',
                    '/webadmin/settlement/list.html',
                    '/webadmin/manpower/list.html',
                    '/webadmin/laborcosts/list.html',
                    '/webadmin/achieve/list.html',
                    '/webadmin/evaluation/list.html',
                    '/webadmin/audit/list.html',
                    '/webadmin/contract/list.html',
                    '/webadmin/expense/list.html',
                    '/webadmin/benefit/list.html'
                );
            }
            // 레벨 7이면 프로그램 등록관리자로 설정 yllee 190313
            $auth_code = 'program';
        }

        for ($i = 0; $i < count($menu); $i++) {
            if (($menu[$i]['uri'] || count($menu[$i]['sub']) > 0)) {
                // 채용공고접수, 성희롱 상담 담당자가 해당 메뉴만 들어 갈 수 있게 처리 wkkim 180430
                if (($auth_code == 'receipt' && $menu[$i]['uri'] != '/webadmin/finance/list.html') || $auth_code != 'receipt') {
                    $nav .= '<li class="nav' . ($i + 1) . '">' . "\n";
                    $nav .= '<!-- depth1 -->' . "\n";
                    $nav .= '<a href="' . $menu[$i]['uri'] . '" title="' . $menu[$i]['title'] . '">';
                    $nav .= $menu[$i]['title'] . '</a>' . "\n";

                    if ($max_depth > 1 && count($menu[$i]['sub']) > 0) {
                        $nav .= '<ul>' . "\n";
                        for ($j = 0; $j < count($menu[$i]['sub']); $j++) {
                            if ((($auth_code == 'sexualreport' || $auth_code == 'dual') && !in_array($menu[$i]['sub'][$j]['uri'],
                                        $menu_sexualreport)) || ($auth_code != 'sexualreport' && $auth_code != 'dual')) {
                                $nav .= '<li class="nav' . ($j + 1) . '">' . "\n";
                                $nav .= '<!-- depth2 -->' . "\n";
                                $nav .= '<a href="' . $menu[$i]['sub'][$j]['uri'] . '" ';
                                $nav .= 'title="' . $menu[$i]['sub'][$j]['title'] . '">';
                                $nav .= $menu[$i]['sub'][$j]['title'] . '</a>' . "\n";

                                if ($max_depth > 2 && count($menu[$i]['sub'][$j]['sub']) > 0) {
                                    $nav .= '<ul>' . "\n";
                                    for ($k = 0; $k < count($menu[$i]['sub'][$j]['sub']); $k++) {
                                        $nav .= '<li class="nav' . ($k + 1) . '">' . "\n";
                                        $nav .= '<!-- depth3 -->' . "\n";
                                        $nav .= '<a href="' . $menu[$i]['sub'][$j]['sub'][$k]['uri'] . '" ';
                                        $nav .= 'title="' . $menu[$i]['sub'][$j]['sub'][$k]['title'] . '">';
                                        $nav .= $menu[$i]['sub'][$j]['sub'][$k]['title'] . '</a>' . "\n";
                                        $nav .= '<!-- //depth3 -->' . "\n";
                                        $nav .= '</li>' . "\n";
                                    }
                                    $nav .= '</ul>' . "\n";
                                }

                                $nav .= '<!-- //depth2 -->' . "\n";
                                $nav .= '</li>' . "\n";
                            }
                        }
                        $nav .= '</ul>' . "\n";
                    }

                    $nav .= '<!-- //depth1 -->' . "\n";
                    $nav .= '</li>' . "\n";
                }
            }
        }
        return $nav;
    }

    /**
     * GNB 생성
     * @return string
     */
    public function makeGnb()
    {
        return $this->makeNavigation($this->pure_menu_arr, 2);
    }

    /**
     * 모바일 GNB 생성
     * @return string
     */
    public function makeMobileGnb()
    {
        return $this->makeMobileNavigation($this->pure_menu_arr, 3);
    }

    /**
     * 관리자 GNB 생성 wkkim 180430
     * @return string
     */
    public function makeAdminGnb()
    {
        return $this->makeAdminNavigation($this->pure_menu_arr, 2);
    }

    /**
     * SNB 생성
     * @return string
     */
    public function makeSnb()
    {
        return $this->makeNavigation($this->all_menu_arr[$this->page_no_arr[0] - 1]['sub'], 2);
    }

    /**
     * 사이트맵 생성
     * @return string
     */
    public function makeSitemap()
    {
        return $this->makeNavigation($this->all_menu_arr, 3);
    }

    /**
     * 관리자 사이트맵 생성 wkkim 180430
     * @return string
     */
    public function makeAdminSitemap()
    {
        return $this->makeAdminNavigation($this->all_menu_arr, 2);
    }
}
