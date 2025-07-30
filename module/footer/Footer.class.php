<?php
/**
 * 캐러셀 모듈 클래스
 * @file    Footer.class.php
 * @author  Alpha-Edu
 * @package
 */
namespace sFramework;

class Footer extends StandardModule
{
    // DB info
    public static $data_table = 'tbl_footer';
    public static $pk = 'ft_id';

    protected function setModuleConfig()
    {
        parent::setModuleConfig();

        $this->set('module', 'footer');
        $this->set('module_name', '하단배너');

        // 검색
        $this->set('search_columns', 'cr_code');
        $this->set('search_like_arr', array(
            'cr_subject'     => '제목'
        ));

        // 정렬
        $this->set('cnt_rows', 9999);
        $this->set('order_column', 'ft_order');
        $this->set('order_direct', 'ASC');

        // 파일
        $this->set('max_file', 1);

        // 썸네일
        $this->set('flag_use_thumb', true);
        $this->set('no_image', null);
        $this->set('thumb_width', '100');

        // code
        $this->set('cr_code_arr', array(
            'popupzone' => '팝업존',
            'banner' => '배너',
            'visual' => '메인비주얼'
        ));
        $this->set('ft_is_display_arr', array(
            'Y' => '출력',
            'N' => '숨김'
        ));
        $this->set('cr_skin_arr', array(
            'basic' => '기본',
            'blue' => '블루'
        ));

        $this->set('img_size', '150 * 43');
    }

    protected function initInsert()
    {
        parent::initInsert();

        $this->set('insert_columns', 'ft_subject,ft_alt,ft_uri,ft_order,ft_is_display');
        $this->set('required_arr', array(
            'ft_subject'   => '제목',
            'ft_is_display'   => '출력여부',
            'ft_order'  => '출력순서',
        ));
    }

    protected function convertInsert($arr)
    {
        $arr = parent::convertInsert($arr);

        // 출력순서
        $data_table = $this->get('data_table');
        $data = Db::selectOnce($data_table, "ft_order", "", "ORDER BY ft_order DESC");
        $arr['ft_order'] = $data['ft_order'] + 1;

        return $arr;
    }

    protected function postInsert($arr)
    {
        $result = parent::postInsert($arr);

        $this->resetOrder();

        return $result;
    }

    protected function initUpdate()
    {
        parent::initUpdate();

        $pk = $this->get('pk');
        $this->set('update_columns', $pk . ',ft_subject,ft_alt,ft_uri,ft_order,ft_is_display,');
        $this->set('required_arr', array(
            'ft_subject'   => '제목',
            'ft_is_display'   => '출력여부',
            'ft_order'  => '출력순서',
        ));
    }

    protected function convertUpdate($arr)
    {
        $arr = parent::convertUpdate($arr);

        return $arr;
    }

    protected function postUpdate($arr)
    {
        $result = parent::postUpdate($arr);

        $this->resetOrder();

        return $result;
    }

    protected function postDelete()
    {
        $result = parent::postDelete();

        $this->resetOrder();

        return $result;
    }

    protected function convertDetail($data)
    {
        $data = parent::convertDetail($data);

        // 대체정보
        $data['img_alt'] = ($data['ft_alt']) ? $data['ft_alt'] : $data['ft_subject'] . '의 이미지';

        // 상태
        $data['state_class'] = 'ft_is_display_' . $data['ft_is_display'];

        // 첨부파일 처리
        $file_list = $data['file_list'];
        $data['carousel_img'] = null;
        for ($i = 0; $i < count($file_list); $i++) {
            $file_type = $file_list[$i]['fi_type'];
            if ($file_type == 'carousel') {
                $data['carousel_img'] = $file_list[$i];
                break;
            }
        }

        if ($data['carousel_img']) {
            $data['img_uri'] = $data['carousel_img']['fi_uri'];
            //$data['thumb_uri'] = $data['carousel_img']['fi_uri'];
        } else {
            $data['img_uri'] = $this->get('no_image');
        }

        return $data;
    }

    protected function makeDbWhere()
    {
        $db_where = parent::makeDbWhere();
        $this->set('db_where', $db_where);

        return $db_where;
    }

    public function changeOrder()
    {
        $result = array(
            'code'  => 'failure'
        );
        $data_table = $this->get('data_table');
        $pk = $this->get('pk');
        $uid = $this->get('uid');
        $direction = $this->getRequestParameter('direction');

        if (!$uid || ($direction != 'up' && $direction != 'down')) {
            $result['msg'] = '비정상적인 접근입니다.';
            return $result;
        }

        $order_column = $this->get('order_column');

        $or_data = Db::selectOnce($data_table, $order_column, "WHERE $pk = '$uid'", "");
        $or_order = $or_data['ft_order'];
        if ($direction == 'up') {
            $tg_data = Db::selectOnce($data_table, $pk . ',' . $order_column, "WHERE $order_column < '$or_order'", "ORDER BY $order_column DESC");
        } elseif ($direction == 'down') {
            $tg_data = Db::selectOnce($data_table, $pk . ',' . $order_column, "WHERE $order_column > '$or_order'", "ORDER BY $order_column");
        }
        $tg_uid = $tg_data[$pk];
        $tg_order = $tg_data[$order_column];

        Db::update($data_table, "$order_column = '$tg_order'", "WHERE $pk = '$uid'");
        Db::update($data_table, "$order_column = '$or_order'", "WHERE $pk = '$tg_uid'");

        $this->resetOrder();

        $result['code'] = 'success';

        return $result;
    }

    protected function resetOrder()
    {
        $data_table = $this->get('data_table');
        $pk = $this->get('pk');
        $order_column = $this->get('order_column');

        $list = Db::select($data_table, "$pk", "", "ORDER BY $order_column ASC", "");
        for ($i = 0; $i < count($list); $i++) {
            $new_order = $i + 1;
            $uid = $list[$i][$pk];
            Db::update($data_table, "$order_column = '$new_order'", "WHERE $pk = '$uid'");
        }
    }

    public function selectDisplayList()
    {
        $data_table = $this->get('data_table');
        $order_column = $this->get('order_column');
        $db_where = "WHERE ft_is_display = 'Y' ";
        $list = Db::select($data_table, "*", $db_where, "ORDER BY $order_column ASC", "");

        return $this->convertList($list);

    }
}
