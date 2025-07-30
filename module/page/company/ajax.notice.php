<?php
/**
 * @file    main.php
 * @author  Alpha-Edu
 */
use sFramework\Html;

if (!defined('_ALPHA_')) {
    exit;
}

?>

        <table class="list_table border whoread">
            <colgroup>
                <col width="40%"><!-- 사업명 -->
                <col width="40%"><!-- 단체명 -->
                <col width="20%"><!-- 읽음여부 -->
            </colgroup>
            <thead>
                <tr>
                    <th>사업명</th>
                    <th>단체명</th>
                    <th>읽음여부</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>{사업사업명}</td>
                    <td><strong>{단체단체명}</strong></td>
                    <td>{0000.00.00}</td>
                </tr>
                <tr>
                    <td>{사업명}</td>
                    <td><strong>{노읽음}</strong></td>
                    <td class="nodata">-</td>
                </tr>
                <tr>
                    <td>{사업명}</td>
                    <td><strong>{인플러스}</strong></td>
                    <td>{0000.00.00}</td>
                </tr>
                <tr>
                    <td>{사업명}</td>
                    <td><strong>{스타코어} </strong></td>
                    <td class="nodata">-</td>
                </tr>
            </tbody>
        </table>
